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

    public static function getMatomoPhpEndpoint(SystemConfigService $systemConfigService): ?string
    {
        $configured = $systemConfigService->getString('TinectMatomo.config.matomoserver');
        if ($configured === '') {
            return null;
        }

        $configured = trim($configured);
        // If already points to matomo.php (with or without trailing slash), use as-is without trailing slash
        if (preg_match('/matomo\.php(\/.+)?$/i', $configured)) {
            return rtrim($configured, '/');
        }

        return rtrim($configured, '/') . '/matomo.php';
    }

    public static function getMatomoJsEndpoint(SystemConfigService $systemConfigService): ?string
    {
        $configured = $systemConfigService->getString('TinectMatomo.config.matomoserver');
        if ($configured === '') {
            return null;
        }

        $configured = trim($configured);
        // If already points to matomo.js (with or without trailing slash), use as-is without trailing slash
        if (preg_match('/matomo\.js(\/.+)?$/i', $configured)) {
            return rtrim($configured, '/');
        }

        return rtrim($configured, '/') . '/matomo.js';
    }
}
