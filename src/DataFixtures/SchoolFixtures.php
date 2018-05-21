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

use App\Entity\Campus;
use App\Entity\Course;
use App\Entity\Department;
use App\Entity\Invoice;
use App\Entity\Scale;
use App\Entity\Setting;
use App\Entity\Space;
use App\Entity\Translate;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Yaml\Yaml;

class SchoolFixtures extends Fixture implements DependentFixtureInterface
{
    use buildTable;


    /**
     * @var array
     */
    private $assoc = [
        'created_by'   => 'createdBy',
        'modified_by' => 'modifiedBy',
        'department_id' => 'department',
    ];
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $data = Yaml::parse(file_get_contents('/SQL/App/campus.yml'));

        $this->buildTable($data, Campus::class, $manager);

        $data = Yaml::parse(file_get_contents('/SQL/App/space.yml'));

        $this->buildTable($data, Space::class, $manager);

        $data = Yaml::parse(file_get_contents('/SQL/App/department.yml'));

        $this->buildTable($data, Department::class, $manager);

        $data = Yaml::parse(file_get_contents('/SQL/App/course.yml'));

        $this->buildTable($data, Course::class, $manager);

        $data = Yaml::parse(file_get_contents('/SQL/App/scale.yml'));

        $this->buildTable($data, Scale::class, $manager);

        $data = Yaml::parse(file_get_contents('/SQL/App/setting.yml'));

        $this->buildTable($data, Setting::class, $manager);

        $data = Yaml::parse(file_get_contents('/SQL/App/translate.yml'));

        $this->buildTable($data, Translate::class, $manager);

        $data = Yaml::parse(file_get_contents('/SQL/App/invoice.yml'));

        $this->buildTable($data, Invoice::class, $manager);
    }

    /**
     * getDependencies
     *
     * @return array
     */
    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}