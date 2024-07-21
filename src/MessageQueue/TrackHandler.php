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

        $matomoUrl .= 'matomo.php';

        $client = new Client();

        $parameter = $message->parameters;
        $parameter['cdt'] = $message->unixTimestamp;
        $parameter['token_auth'] = $authToken;

        if (!empty($message->clientIp) && $message->clientIp !== '::1') {
            $parameter['cip'] = $message->clientIp;
        }

        $client->post($matomoUrl, [
            'form_params' => $parameter,
            'headers' => [
                'User-Agent' => $message->userAgent,
                'Accept-Language' => str_replace(["\n", "\t", "\r"], '', $message->acceptLanguage),
            ],
            'timeout' => 5,
        ]);
    }
}
