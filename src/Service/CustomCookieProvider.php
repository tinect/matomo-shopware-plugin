<?php declare(strict_types=1);

namespace Tinect\Matomo\Service;

use Shopware\Storefront\Framework\Cookie\CookieProviderInterface;

class CustomCookieProvider implements CookieProviderInterface
{
    private const cookieGroup = [
        'snippet_name' => 'cookie.groupStatistical',
        'snippet_description' => 'cookie.groupStatisticalDescription',
        'entries' => [
            [
                'snippet_name' => 'cookie.MatomoAnalytics',
                'snippet_description' => 'cookie.MatomoDescription',
                'cookie' => 'matomo-analytics-enabled',
                'expiration' => '30',
                'value' => '1',
            ],
        ],
    ];

    public function __construct(private readonly CookieProviderInterface $originalService)
    {
    }

    public function getCookieGroups(): array
    {
        $cookieGroups = $this->originalService->getCookieGroups();

        foreach ($cookieGroups as &$group) {
            if (!\is_array($group) || !isset($group['snippet_name'])) {
                continue;
            }

            if ($group['snippet_name'] === self::cookieGroup['snippet_name']) {
                $group['entries'] = array_merge(
                    $group['entries'],
                    self::cookieGroup['entries']
                );

                return $cookieGroups;
            }
        }

        return array_merge(
            $cookieGroups,
            [
                self::cookieGroup,
            ]
        );
    }
}
