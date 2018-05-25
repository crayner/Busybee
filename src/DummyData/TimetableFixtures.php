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

use App\Entity\Timetable;
use App\Entity\TimetableAssignedDay;
use App\Entity\TimetableColumn;
use App\Entity\TimetableDay;
use App\Entity\TimetableLine;
use App\Entity\TimetablePeriod;
use Doctrine\Common\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Yaml\Yaml;

class TimetableFixtures implements DummyDataInterface
{
    use buildTable;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager, LoggerInterface $logger)
    {
        $data = Yaml::parse(file_get_contents(__DIR__ . '/SQL/App/tt_line.yml'));

        $this->setLogger($logger)->buildTable($data, TimetableLine::class, $manager);

        $data = Yaml::parse(file_get_contents(__DIR__ . '/SQL/App/tt.yml'));

        $this->buildTable($data, Timetable::class, $manager);

        $data = Yaml::parse(file_get_contents(__DIR__ . '/SQL/App/tt_column.yml'));

        $this->buildTable($data, TimetableColumn::class, $manager);

        $data = Yaml::parse(file_get_contents(__DIR__ . '/SQL/App/tt_assigned_day.yml'));

        $this->buildTable($data, TimetableAssignedDay::class, $manager);

        $data = Yaml::parse(file_get_contents(__DIR__ . '/SQL/App/tt_day.yml'));

        $this->buildTable($data, TimetableDay::class, $manager);

        $data = Yaml::parse(file_get_contents(__DIR__ . '/SQL/App/tt_period.yml'));

        $this->buildTable($data, TimetablePeriod::class, $manager);
    }
}