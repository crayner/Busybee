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

use App\Entity\Campus;
use App\Entity\Course;
use App\Entity\Department;
use App\Entity\Invoice;
use App\Entity\Scale;
use App\Entity\Setting;
use App\Entity\Space;
use App\Entity\Translate;
use Doctrine\Common\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Yaml\Yaml;

class SchoolFixtures implements DummyDataInterface
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
        $data = Yaml::parse(file_get_contents(__DIR__ . '/SQL/App/campus.yml'));

        $this->setLogger($logger)->buildTable($data, Campus::class, $manager);

        $data = Yaml::parse(file_get_contents(__DIR__ . '/SQL/App/space.yml'));

        $this->buildTable($data, Space::class, $manager);

        $data = Yaml::parse(file_get_contents(__DIR__ . '/SQL/App/department.yml'));

        $this->buildTable($data, Department::class, $manager);

        $data = Yaml::parse(file_get_contents(__DIR__ . '/SQL/App/course.yml'));

        $this->buildTable($data, Course::class, $manager);

        $data = Yaml::parse(file_get_contents(__DIR__ . '/SQL/App/scale.yml'));

        $this->buildTable($data, Scale::class, $manager);

        $data = Yaml::parse(file_get_contents(__DIR__ . '/SQL/App/setting.yml'));

        $this->buildTable($data, Setting::class, $manager);

        $data = Yaml::parse(file_get_contents(__DIR__ . '/SQL/App/translate.yml'));

        $this->buildTable($data, Translate::class, $manager);

        $data = Yaml::parse(file_get_contents(__DIR__ . '/SQL/App/invoice.yml'));

        $this->buildTable($data ?: [], Invoice::class, $manager);
    }
}