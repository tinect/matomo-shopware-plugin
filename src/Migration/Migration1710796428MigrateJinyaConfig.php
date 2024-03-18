<?php

declare(strict_types=1);

namespace Tinect\Matomo\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1710796428MigrateJinyaConfig extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1710796428;
    }

    public function update(Connection $connection): void
    {
        $existingConfig = $connection->fetchAllAssociative('SELECT * FROM system_config WHERE configuration_key LIKE "JinyaMatomo.config.%"');

        if (count($existingConfig) === 0) {
            return;
        }

        $connection->executeStatement('DELETE FROM system_config WHERE configuration_key LIKE "TinectMatomo.config.%"');
        $connection->executeStatement('UPDATE system_config SET configuration_key = REPLACE(configuration_key, \'JinyaMatomo.config.\', \'TinectMatomo.config.\') WHERE configuration_key LIKE "JinyaMatomo.config.%"');
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
