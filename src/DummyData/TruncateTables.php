<?php
/**
 * Created by PhpStorm.
 *
 * This file is part of the Busybee Project.
 *
 * (c) Craig Rayner <craig@craigrayner.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * User: craig
 * Date: 21/05/2018
 * Time: 18:41
 */

namespace App\DummyData;


use Doctrine\Common\Persistence\ObjectManager;

class TruncateTables
{
    /**
     * execute
     *
     * @param ObjectManager $objectManager
     */
    public function execute(ObjectManager $objectManager)
    {
        // initialize the database connection
        $connection = $objectManager->getConnection();
        $schemaManager = $connection->getSchemaManager();
        $tables = $schemaManager->listTables();

        $sql = 'SET FOREIGN_KEY_CHECKS = 0;';
        $connection->exec($sql);

        foreach ($tables as $table)
        {
            if (mb_strpos($table->getName(), '_user') === false) {
                $sql = sprintf('TRUNCATE TABLE %s', $table->getName());
                $connection->exec($sql);
            } else {
                $sql = sprintf('DELETE FROM `%s` WHERE `id` > 1', $table->getName());
                $connection->exec($sql);
                $sql = sprintf('ALTER TABLE `%s` AUTO_INCREMENT = 1', $table->getName());
                $connection->exec($sql);
            }
        }
        $sql = 'SET FOREIGN_KEY_CHECKS = 1;';
        $connection->exec($sql);
    }
}
