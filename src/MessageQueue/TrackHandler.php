<?php declare(strict_types=1);

namespace Tinect\Matomo\MessageQueue;

use GuzzleHttp\Client;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Tinect\Matomo\Service\StaticHelper;

#[AsMessageHandler]
class TrackHandler
{
    public function __construct(
        private readonly SystemConfigService $systemConfigService
    ) {
    }

    public function __invoke(TrackMessage $message): void
    {
        $authToken = $this->systemConfigService->getString('TinectMatomo.config.matomoauthtoken');

        if ($authToken === '') {
            return;
        }

        $matomoUrl = StaticHelper::getMatomoUrl($this->systemConfigService);

        if ($matomoUrl === null) {
            return;
        }

        $queryParameters = [];
        $queryParameters['cdt'] = $message->unixTimestamp;
        $queryParameters['token_auth'] = $authToken;

        if (!empty($message->clientIp) && $message->clientIp !== '::1') {
            $queryParameters['cip'] = $message->clientIp;
        }

        $matomoUrl .= 'matomo.php';

        $parameter = $message->parameters;

        if (\is_string($parameter)) {
            try {
                $parameter = $this->enrichRequests($parameter, $queryParameters);
            } catch (\JsonException) {
            }

            $data = [
                'body' => $parameter,
            ];
        } else {
            $data = [
                'form_params' => $parameter,
            ];
        }

        $client = new Client();

        $client->post($matomoUrl . '?' . \http_build_query($queryParameters), [
            ...$data,
            'headers' => [
                'User-Agent' => $message->userAgent,
                'Accept-Language' => str_replace(["\n", "\t", "\r"], '', $message->acceptLanguage),
            ],
            'timeout' => 10,
        ]);
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

        $result = \json_encode($parameter, JSON_THROW_ON_ERROR);

        if (is_string($result)) {
            return $result;
        }

        return '';
    }
}
