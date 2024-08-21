<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Propel\Business\Model\PropelDatabase\Adapter\MySql;

use PDO;
use Spryker\Shared\Config\Config;
use Spryker\Shared\Propel\PropelConstants;
use Spryker\Zed\Propel\Business\Model\PropelDatabase\Command\CreateDatabaseInterface;

class CreateMySqlDatabase implements CreateDatabaseInterface
{
    /**
     * @return void
     */
    public function createIfNotExists(): void
    {
    }

    /**
     * @return \PDO
     */
    protected function getConnection(): PDO
    {
        return new PDO(
            $this->getDatabaseSourceName(),
        );
    }

    /**
     * @return string
     */
    protected function getDatabaseSourceName(): string
    {
        $propelConfig = Config::get(PropelConstants::PROPEL);

        return $propelConfig['database']['connections']['default']['dsn'];
    }
}
