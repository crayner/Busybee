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
 * Date: 20/05/2018
 * Time: 10:57
 */
namespace App\DummyData;

use Doctrine\Common\Persistence\ObjectManager;
use Hillrange\Security\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\Yaml\Yaml;

class UserFixtures implements DummyDataInterface
{
    use buildTable;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     * @param LoggerInterface $logger
     */
    public function load(ObjectManager $manager, LoggerInterface $logger)
    {
        // Bundle to manage file and directories

        $data = Yaml::parse(file_get_contents(__DIR__ . '/SQL/App/hrs_user.yml'));
        $data = $data ?: [];
        foreach($data as $q=>$user) {
            if (intval($user['id']) === 1) {
                unset($data[$q]);
                break;
            }
        }

        $this->setLogger($logger)->buildTable($data, User::class, $manager);
    }
}
