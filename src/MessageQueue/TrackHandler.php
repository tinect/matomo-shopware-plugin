<?php declare(strict_types=1);

namespace Tinect\Matomo\MessageQueue;

use GuzzleHttp\Client;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Tinect\Matomo\Service\ConditionalLogger;
use Tinect\Matomo\Service\StaticHelper;

#[AsMessageHandler]
class TrackHandler
{
    public function __construct(
        private readonly SystemConfigService $systemConfigService,
        private readonly ConditionalLogger $logger
    ) {
    }

    public function __invoke(TrackMessage $message): void
    {
        $authToken = $this->systemConfigService->getString('TinectMatomo.config.matomoauthtoken');

        if ($authToken === '') {
            $this->logger->error('No auth token configured for Matomo tracking.');

            return;
        }

        $matomoUrl = StaticHelper::getMatomoPhpEndpoint($this->systemConfigService);

        if ($matomoUrl === null) {
            $this->logger->error('No matomo url configured for Matomo tracking.');

            return;
        }

        $commonParameters = [];
        $commonParameters['apiv'] = 1;
        $commonParameters['cdt'] = $message->unixTimestamp;
        $commonParameters['token_auth'] = $authToken;

        if (!empty($message->clientIp) && $message->clientIp !== '::1') {
            $commonParameters['cip'] = $message->clientIp;
        }

        $parameter = $message->parameters;

        if (\is_string($parameter)) {
            // Bulk tracking payload: enrich each request; do NOT also put params on URL
            try {
                $parameter = $this->enrichRequests($parameter, $commonParameters);
            } catch (\JsonException) {
                parse_str($parameter, $parsedQuery);
                if ($parsedQuery !== []) {
                    $parameter = $parsedQuery;
                }
            }

            $data = [
                'body' => $parameter,
            ];
        }

        if (\is_array($parameter)) {
            $merged = array_merge($parameter, $commonParameters);
            // Remove empty-string values to avoid invalid parameters like _id=""
            $merged = array_filter($merged, static function ($v) {
                return $v !== '' && $v !== null;
            });

            $data = [
                'form_params' => $merged,
            ];
        }

        if (!isset($data)) {
            $this->logger->error('Parameters for Matomo tracking could not be handled.', [
                'parameters' => $message->parameters,
            ]);

            throw new \RuntimeException('Parameters for Matomo tracking could not be handled.');
        }

        $client = new Client();

        // Build headers and set JSON content type when sending bulk payload
        $headers = [
            'User-Agent' => $message->userAgent,
            'Accept-Language' => mb_substr(
                preg_replace('/[^\x20-\x7E,;=\-]/', '', $message->acceptLanguage),
                0,
                255
            ),
        ];
        if (isset($data['body'])) {
            $headers['Content-Type'] = 'application/json';
        }

        // Post directly to matomo.php without query string; params are in body
        try {
            $response = $client->post($matomoUrl, [
                ...$data,
                'headers' => $headers,
                'timeout' => 10,
                'http_errors' => false, // allow logging for all HTTP statuses
            ]);

            $status = $response->getStatusCode();
            $body = (string) $response->getBody();

            if ($this->logger->isDebugEnabled()) {
                // sanitize data before logging (redact token_auth)
                $safeData = $this->sanitizeDataForLog($data);
                $this->logger->debug('Matomo tracking response', [
                    'status' => $status,
                    'responseBody' => mb_substr($body, 0, 500),
                    'endpoint' => $matomoUrl,
                    'payload' => $safeData,
                    'headers' => $headers,
                ]);
            }

            if ($status >= 200 && $status < 300) {
                return; // success
            }

            // Matomo answers cip/token-authenticated tracking requests that it
            // transiently rejects (e.g. token not accepted at that moment) with an
            // HTTP 400 carrying its "This resource is part of Matomo" landing page
            // and records nothing. This is retryable: the same request usually
            // succeeds against a healthy Matomo, and ecommerce re-sends are
            // idempotent (Matomo deduplicates orders by ec_id).
            $isEmptyRequestResponse = $status === 400
                && stripos($body, 'This resource is part of Matomo') !== false;

            // Treat genuinely permanent 4xx (403/404/413/...) as unrecoverable to
            // avoid endless retries; the transient "empty request" 400 falls through.
            if ($status >= 400 && $status < 500 && !$isEmptyRequestResponse) {
                throw new UnrecoverableMessageHandlingException('Matomo returned HTTP ' . $status . ' with body: ' . mb_substr($body, 0, 500));
            }

            // For 5xx, unexpected statuses, and the transient "empty request" 400,
            // throw a retryable exception so the worker retries with backoff.
            throw new \RuntimeException('Matomo returned HTTP ' . $status . ' with body: ' . mb_substr($body, 0, 500));
        } catch (\Throwable $e) {
            $safeData = $this->sanitizeDataForLog($data);
            $context = [
                'exception' => $e::class,
                'message' => $e->getMessage(),
                'endpoint' => $matomoUrl,
                'payload' => $safeData,
            ];

            // Unrecoverable failures are given up on (dead-lettered) -> ERROR.
            // Everything else is transient and will be retried by the worker,
            // so log at WARNING to avoid alarming on self-healing hiccups.
            if ($e instanceof UnrecoverableMessageHandlingException) {
                $this->logger->error('Matomo tracking request error (unrecoverable)', $context);
            } else {
                $this->logger->warning('Matomo tracking request error (transient, will retry)', $context);
            }

            throw $e; // let worker handle retries for transient issues
        }
    }

    private function enrichRequests(string $parameter, array $queryParameters): string
    {
        $parameter = \json_decode($parameter, true, 512, \JSON_THROW_ON_ERROR);

        if (isset($parameter['requests']) && \is_array($parameter['requests'])) {
            foreach ($parameter['requests'] as &$request) {
                $request .= '&' . \http_build_query($queryParameters);
            }
            unset($request);
        }

        return \json_encode($parameter, \JSON_THROW_ON_ERROR);
    }

    private function sanitizeDataForLog(array $data): array
    {
        $sanitizeFields = ['token_auth'];
        $copy = $data;

        foreach ($sanitizeFields as $field) {
            if (isset($copy['form_params'][$field])) {
                $copy['form_params'][$field] = '***redacted***';
            }
            if (isset($copy['body']) && \is_string($copy['body'])) {
                $copy['body'] = preg_replace('/' . $field . '=[^&"]+/i', $field . '=***redacted***', $copy['body']);
            }
        }

        return $copy;
    }
}
