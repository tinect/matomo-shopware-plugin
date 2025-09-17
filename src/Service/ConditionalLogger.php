<?php declare(strict_types=1);

namespace Tinect\Matomo\Service;

use Psr\Log\LoggerInterface;
use Shopware\Core\System\SystemConfig\SystemConfigService;

/**
 * Small helper that only forwards log calls to the underlying PSR logger when
 * a logger is available AND the plugin config flag `enableLogger` is enabled.
 */
class ConditionalLogger
{
    public function __construct(
        private readonly SystemConfigService $systemConfigService,
        private readonly ?LoggerInterface $logger = null
    ) {
    }

    public function isEnabled(): bool
    {
        return $this->logger !== null
            && $this->systemConfigService->getBool('TinectMatomo.config.enableLogger');
    }

    /** @param array<string, mixed> $context */
    public function info(string $message, array $context = []): void
    {
        if ($this->isEnabled()) {
            $this->logger?->info($message, $context);
        }
    }

    /** @param array<string, mixed> $context */
    public function error(string $message, array $context = []): void
    {
        if ($this->isEnabled()) {
            $this->logger?->error($message, $context);
        }
    }

    /** @param array<string, mixed> $context */
    public function warning(string $message, array $context = []): void
    {
        if ($this->isEnabled()) {
            $this->logger?->warning($message, $context);
        }
    }

    /** @param array<string, mixed> $context */
    public function debug(string $message, array $context = []): void
    {
        if ($this->isEnabled()) {
            $this->logger?->debug($message, $context);
        }
    }
}
