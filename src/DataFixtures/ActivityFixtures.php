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

use App\Entity\Activity;
use App\Entity\ActivitySlot;
use App\Entity\ActivityStudent;
use App\Entity\ActivityTutor;
use App\Entity\CalendarGrade;
use App\Entity\ExternalActivity;
use App\Entity\Term;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Yaml\Yaml;

class ActivityFixtures
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
        'space_id' => [
            'name' => 'space',
            'method' => 'setSpace',
            ],
        'student_reference_id' => [
            'name' => 'studentReference',
            'method' => 'setStudentReference',
            ],
        'tt_line_id' => [
            'name' => 'line',
            'method' => 'setLine',
            ],
        'course_id' => [
            'name' => 'course',
            'method' => 'setCourse',
            ],
        'activity_slot_id' => [
            'name' => 'activity',
            'method' => 'setActivity',
            ],
        'activity_id' => [
            'name' => 'activity',
            'method' => 'setActivity',
            ],
        'external_activity_backup_id' => [
            'name' => 'externalActivityBackup',
            'method' => 'setExternalActivityBackup',
            ],
        'external_activity_invoice_id' => [
            'name' => 'externalInvoiceID',
            'method' => 'setExternalInvoiceID',
            ],
        'student_id' => [
            'name' => 'student',
            'method' => 'setStudent',
            ],
        'tutor_id' => [
            'name' => 'tutor',
            'method' => 'setTutor',
            ],
    ];

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $data = Yaml::parse(file_get_contents(__DIR__ . '/SQL/App/activity.yml'));

        $this->buildTable($data, Activity::class, $manager);

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

        $data = Yaml::parse(file_get_contents(__DIR__ . '/SQL/App/activity_student.yml'));

        $this->buildTable($data, ActivityStudent::class, $manager);
    }

    /**
     * getDependencies
     *
     * @return array
     */
    public function getDependencies()
    {
        return [
            TimetableFixtures::class,
            CalendarFixtures::class,
            SchoolFixtures::class,
            UserFixtures::class,
            PeopleFixtures::class,
        ];
    }
}