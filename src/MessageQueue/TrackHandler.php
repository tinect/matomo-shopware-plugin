<?php declare(strict_types=1);

namespace Tinect\Matomo\MessageQueue;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Tinect\Matomo\Service\StaticHelper;
use Tinect\Matomo\Service\ConditionalLogger;

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
                $this->logger->error('No matomo url configured for Matomo tracking.');
            }

            $data = [
                'body' => $parameter,
            ];
        } else {
            $merged = array_merge($parameter, $commonParameters);
            // Remove empty-string values to avoid invalid parameters like _id=""
            $merged = array_filter($merged, static function ($v) {
                return $v !== '' && $v !== null;
            });

            $data = [
                'form_params' => $merged,
            ];
        }

        $client = new Client();

        // Build headers and set JSON content type when sending bulk payload
        $headers = [
            'User-Agent' => $message->userAgent,
            'Accept-Language' => str_replace(["\n", "\t", "\r"], '', $message->acceptLanguage),
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
                ]);
            }

            if ($status >= 200 && $status < 300) {
                return; // success
            }

            // treat 4xx as unrecoverable to avoid endless retries
            if ($status >= 400 && $status < 500) {
                throw new UnrecoverableMessageHandlingException('Matomo returned HTTP ' . $status . ' for tracking payload');
            }

            // For 5xx and other unexpected statuses, throw to let worker retry
            throw new \RuntimeException('Matomo returned HTTP ' . $status . ' for tracking payload');
        } catch (\Throwable $e) {
            $safeData = $this->sanitizeDataForLog($data);
            $this->logger->error('Matomo tracking request error (transport/other)', [
                'exception' => $e::class,
                'message' => $e->getMessage(),
                'endpoint' => $matomoUrl,
                'payload' => $safeData,
            ]);
            throw $e; // let worker handle retries for transient issues
        }
    }

    private function enrichRequests(string $parameter, array $queryParameters): string
    {
        $parameter = \json_decode($parameter, true, 512, JSON_THROW_ON_ERROR);

        if (isset($parameter['requests']) && is_array($parameter['requests'])) {
            foreach ($parameter['requests'] as &$request) {
                $request .= '&' . \http_build_query($queryParameters);
            }
            unset($request);
        }

        return \json_encode($parameter, JSON_THROW_ON_ERROR);
    }

    private function sanitizeDataForLog(array $data): array
    {
        $copy = $data;
        if (isset($copy['form_params']) && is_array($copy['form_params'])) {
            if (array_key_exists('token_auth', $copy['form_params'])) {
                $copy['form_params']['token_auth'] = '***redacted***';
            }
        }
        if (isset($copy['body']) && is_string($copy['body'])) {
            // mask token_auth values inside bulk body
            $copy['body'] = preg_replace('/token_auth=[^&"]+/i', 'token_auth=***redacted***', $copy['body']);
        }
        return $copy;
    }
}
