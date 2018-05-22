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
namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Hillrange\Security\Entity\User;
use Symfony\Component\Yaml\Yaml;

class UserFixtures extends Fixture
{
    use buildTable;

    /**
     * @var array
     */
    private $assoc = [
        'created_by'   => [
            'name' => 'createdBy',
            'method' => 'setCreatedBy'
        ],
        'modified_by' => [
            'name' => 'modifiedBy',
            'method' => 'setModifiedBy',
        ],
    ];

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        // Bundle to manage file and directories

        $data = Yaml::parse(file_get_contents(__DIR__ . '/SQL/App/hrs_user.yml'));

        $this->buildTable($data, User::class, $manager);
    }
}
