<?php

declare(strict_types=1);

namespace Tinect\Matomo\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1714244310CompleteMatomoServer extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1714244310;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('UPDATE system_config SET
                    configuration_value = JSON_SET(configuration_value, "$._value", CONCAT("https://", JSON_UNQUOTE(JSON_EXTRACT(configuration_value, "$._value"))))
                    WHERE configuration_key = "TinectMatomo.config.matomoserver" AND
                          JSON_UNQUOTE(JSON_EXTRACT(configuration_value, "$._value")) NOT LIKE "http%"');
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
