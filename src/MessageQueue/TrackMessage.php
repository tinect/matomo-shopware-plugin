<?php declare(strict_types=1);

namespace Tinect\Matomo\MessageQueue;

use Shopware\Core\Framework\MessageQueue\LowPriorityMessageInterface;

class TrackMessage implements LowPriorityMessageInterface
{
    public function __construct(
        public readonly ?string $clientIp,
        public readonly string $userAgent,
        public readonly string $acceptLanguage,
        public readonly int $unixTimestamp,
        public readonly array $parameters,
    ) {
    }
}
