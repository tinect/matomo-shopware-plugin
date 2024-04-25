<?php declare(strict_types=1);

namespace Tinect\Matomo\Service;

use Shopware\Core\System\SystemConfig\SystemConfigService;

class StaticHelper
{
    public static function getMatomoUrl(SystemConfigService $systemConfigService): ?string
    {
        $matomoServer = $systemConfigService->getString('TinectMatomo.config.matomoserver');

        if ($matomoServer === '') {
            return null;
        }

        return \rtrim($matomoServer, '/') . '/';
    }
}
