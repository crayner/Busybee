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

use App\Entity\Calendar;
use App\Entity\CalendarGrade;
use App\Entity\Course;
use App\Entity\SpecialDay;
use App\Entity\Term;
use Doctrine\Common\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Yaml\Yaml;

class CalendarFixtures implements DummyDataInterface
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
        $data = Yaml::parse(file_get_contents(__DIR__ . '/SQL/App/calendar.yml'));

        $this->setLogger($logger)->buildTable($data, Calendar::class, $manager);

        $data = Yaml::parse(file_get_contents(__DIR__ . '/SQL/App/calendar_grade.yml'));

        $this->buildTable($data, CalendarGrade::class, $manager);

        $data = Yaml::parse(file_get_contents(__DIR__ . '/SQL/App/calendar_term.yml'));

        $this->buildTable($data, Term::class, $manager);

        $data = Yaml::parse(file_get_contents(__DIR__ . '/SQL/App/calendar_special_day.yml'));

        $this->buildTable($data, SpecialDay::class, $manager);

        $data = Yaml::parse(file_get_contents(__DIR__ . '/SQL/App/course_calendar_grade.yml'));

        $this->buildJoinTable($data ?: [], Course::class, CalendarGrade::class,
            'course_id', 'calendar_grade_id', 'addCalendarGrade', $manager);
    }
}