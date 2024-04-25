<?php declare(strict_types=1);

namespace Tinect\Matomo\MessageQueue;

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

        $matomoUrl .= 'matomo.php?';

        $parameter = $message->parameters;
        $parameter['cdt'] = $message->unixTimestamp;
        $parameter['token_auth'] = $authToken;

        if (!empty($message->clientIp) && $message->clientIp !== '::1') {
            $parameter['cip'] = $message->clientIp;
        }

        foreach ($parameter as $key => $value) {
            $matomoUrl .= $key . '=' . urlencode((string) $value) . '&';
        }

        $stream_options = ['http' => [
            'user_agent' => $message->userAgent,
            'header' => 'Accept-Language: ' . str_replace(["\n", "\t", "\r"], '', $message->acceptLanguage) . "\r\n",
            'timeout' => 5,
        ]];
        $ctx = stream_context_create($stream_options);

        file_get_contents($matomoUrl, false, $ctx);
    }
}
