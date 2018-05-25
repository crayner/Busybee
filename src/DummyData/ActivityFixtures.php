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

use App\Entity\Activity;
use App\Entity\ActivitySlot;
use App\Entity\ActivityStudent;
use App\Entity\ActivityTutor;
use App\Entity\CalendarGrade;
use App\Entity\ExternalActivity;
use App\Entity\Term;
use App\Entity\TimetablePeriodActivity;
use App\Entity\TimetablePeriodActivityTutor;
use Doctrine\Common\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Yaml\Yaml;

class ActivityFixtures implements DummyDataInterface
{
    use buildTable;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager, LoggerInterface $logger)
    {
        $data = Yaml::parse(file_get_contents(__DIR__ . '/SQL/App/activity.yml'));

        $this->setLogger($logger)->buildTable($data, Activity::class, $manager);

        $data = Yaml::parse(file_get_contents(__DIR__ . '/SQL/App/activity_calendar_grade.yml'));

        $this->buildJoinTable($data ?: [], Activity::class, CalendarGrade::class,
            'activity_id', 'calendar_grade_id', 'addCalendarGrade', $manager);

        $data = Yaml::parse(file_get_contents(__DIR__ . '/SQL/App/activity_slot.yml'));

        $this->buildTable($data, ActivitySlot::class, $manager);

        $data = Yaml::parse(file_get_contents(__DIR__ . '/SQL/App/activity_term.yml'));

        $this->buildJoinTable($data ?: [], ExternalActivity::class, Term::class,
            'activity_id', 'term_id', 'addTerm', $manager);

        $data = Yaml::parse(file_get_contents(__DIR__ . '/SQL/App/activity_tutor.yml'));

        $this->buildTable($data, ActivityTutor::class, $manager);

        $data = Yaml::parse(file_get_contents(__DIR__ . '/SQL/App/tt_period_activity.yml'));

        $this->buildTable($data ?: [], TimetablePeriodActivity::class, $manager);

        $data = Yaml::parse(file_get_contents(__DIR__ . '/SQL/App/tt_period_activity_tutor.yml'));

        $this->buildTable($data ?: [], TimetablePeriodActivityTutor::class, $manager);
    }
}