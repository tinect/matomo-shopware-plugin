<?php declare(strict_types=1);

namespace Tinect\Matomo\Service;

use Psr\Log\LoggerInterface;
use Shopware\Core\System\SystemConfig\SystemConfigService;

readonly class ConditionalLogger
{
    public function __construct(
        private SystemConfigService $systemConfigService,
        private LoggerInterface $logger
    ) {
    }

    public function isDebugEnabled(): bool
    {
        return $this->systemConfigService->getBool('TinectMatomo.config.enableDebugLogger');
    }

    /**
     * @param array<string, mixed> $context
     */
    public function error(string $message, array $context = []): void
    {
        $this->logger->error($message, $context);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function debug(string $message, array $context = []): void
    {
        $this->logger->debug($message, $context);
    }
}
