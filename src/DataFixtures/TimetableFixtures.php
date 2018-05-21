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

use App\Entity\Timetable;
use App\Entity\TimetableAssignedDay;
use App\Entity\TimetableColumn;
use App\Entity\TimetableDay;
use App\Entity\TimetableLine;
use App\Entity\TimetablePeriod;
use App\Entity\TimetablePeriodActivity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Yaml\Yaml;

class TimetableFixtures extends Fixture implements DependentFixtureInterface
{
    use buildTable;

    /**
     * @var array
     */
    private $assoc = [
        'calendar_id' => 'calendar',
        'created_by'   => 'createdBy',
        'modified_by' => 'modifiedBy',
        'timetable_id' => 'timetable',
        'term_id' => 'term',
        'tt_column_id' => 'column',
        'special_day_id' => 'specialDay',
        'space_id' => 'space',
        'activity_is' => 'activity',
        'tt_period_id' => 'period',
        'tutor_id' => 'tutor',
    ];

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $data = Yaml::parse(file_get_contents('/SQL/App/tt_line.yml'));

        $this->buildTable($data, TimetableLine::class, $manager);

        $data = Yaml::parse(file_get_contents('/SQL/App/tt.yml'));

        $this->buildTable($data, Timetable::class, $manager);

        $data = Yaml::parse(file_get_contents('/SQL/App/tt_column.yml'));

        $this->buildTable($data, TimetableColumn::class, $manager);

        $data = Yaml::parse(file_get_contents('/SQL/App/tt_assigned_day.yml'));

        $this->buildTable($data, TimetableAssignedDay::class, $manager);

        $data = Yaml::parse(file_get_contents('/SQL/App/tt_day.yml'));

        $this->buildTable($data, TimetableDay::class, $manager);

        $data = Yaml::parse(file_get_contents('/SQL/App/tt_period.yml'));

        $this->buildTable($data, TimetablePeriod::class, $manager);

        $data = Yaml::parse(file_get_contents('/SQL/App/tt_period_activity.yml'));

        $this->buildTable($data, TimetablePeriodActivity::class, $manager);

        $data = Yaml::parse(file_get_contents('/SQL/App/tt_period_activity_tutor.yml'));

        $this->buildTable($data, TimetablePeriodActivity::class, $manager);
    }

    /**
     * getDependencies
     *
     * @return array
     */
    public function getDependencies()
    {
        return [
            PeopleFixtures::class,
        ];
    }
}