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

use App\Calendar\Util\CalendarManager;
use App\Entity\CalendarGrade;
use App\Entity\Course;
use App\Entity\SpecialDay;
use App\Entity\Term;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Yaml\Yaml;

class CalendarFixtures extends Fixture implements DependentFixtureInterface
{
    use buildTable;

    /**
     * @var array
     */
    private $assoc = [
        'calendar_id' => 'calendar',
        'created_by'   => 'createdBy',
        'modified_by' => 'modifiedBy',
    ];

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $data = Yaml::parse(file_get_contents('/SQL/App/calendar.yml'));

        $this->buildTable($data, CalendarManager::class, $manager);

        $data = Yaml::parse(file_get_contents('/SQL/App/calendar_grade.yml'));

        $this->buildTable($data, CalendarGrade::class, $manager);

        $data = Yaml::parse(file_get_contents('/SQL/App/calendar_term.yml'));

        $this->buildTable($data, Term::class, $manager);

        $data = Yaml::parse(file_get_contents('/SQL/App/calendar_special_day.yml'));

        $this->buildTable($data, SpecialDay::class, $manager);

        $data = Yaml::parse(file_get_contents('/SQL/App/course_calendar_grade.yml'));

        $this->buildJoinTable($data ?: [], Course::class, CalendarGrade::class,
            'course_id', 'calendar_grade_id', 'addCalendarGrade', $manager);
    }

    /**
     * getDependencies
     *
     * @return array
     */
    public function getDependencies()
    {
        return [
            SchoolFixtures::class,
        ];
    }
}