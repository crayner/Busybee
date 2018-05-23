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

use App\Entity\Address;
use App\Entity\CalendarGradeStudent;
use App\Entity\CareGiver;
use App\Entity\DepartmentMember;
use App\Entity\Family;
use App\Entity\Locality;
use App\Entity\Person;
use App\Entity\Phone;
use App\Entity\Student;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Yaml\Yaml;

class PeopleFixtures
{
    use buildTable;

    /**
     * @var array
     */
    private $assoc = [
        'user_id' => [
            'name' => 'user',
            'method' => 'setUser',
            ],
        'created_by'   => [
            'name' => 'createdBy',
            'method' => 'setCreatedBy'
            ],
        'modified_by' => [
            'name' => 'modifiedBy',
            'method' => 'setModifiedBy',
            ],
        'person_id' => [
            'name' => 'person',
            'method' => 'setPerson',
            ],
        'student_id' => [
            'name' => 'student',
            'method' => 'setStudent',
            ],
        'calendar_grade_id' => [
            'name' => 'calendarGrade',
            'method' => 'setCalendarGrade',
            ],
        'locality_id' => [
            'name' => 'locality',
            'method' => 'setLocality',
            ],
        'family_id' => [
            'name' => 'family',
            'method' => 'setFamily',
            ],
        'dept_id' => [
            'name' => 'department',
            'method' => 'setDepartment',
            ],
        'staff_id' => [
            'name' => 'staff',
            'method' => 'setStaff',
            ],
    ];

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $data = Yaml::parse(file_get_contents(__DIR__ . '/SQL/App/locality.yml'));

        $this->buildTable($data ?: [], Locality::class, $manager);

        $data = Yaml::parse(file_get_contents(__DIR__ . '/SQL/App/address.yml'));

        $this->buildTable($data ?: [], Address::class, $manager);

        $data = Yaml::parse(file_get_contents(__DIR__ . '/SQL/App/person.yml'));

        $this->buildTable($data, Person::class, $manager);

        $data = Yaml::parse(file_get_contents(__DIR__ . '/SQL/App/family.yml'));

        $this->buildTable($data, Family::class, $manager);

        $data = Yaml::parse(file_get_contents(__DIR__ . '/SQL/App/care_giver.yml'));

        $this->buildTable($data, CareGiver::class, $manager);

        $data = Yaml::parse(file_get_contents(__DIR__ . '/SQL/App/calendar_grade_student.yml'));

        $this->buildTable($data, CalendarGradeStudent::class, $manager);

        $data = Yaml::parse(file_get_contents(__DIR__ . '/SQL/App/department_member.yml'));

        $this->buildTable($data, DepartmentMember::class, $manager);

        $data = Yaml::parse(file_get_contents(__DIR__. '/../DataFixtures/SQL/App/family_student.yml'));

        $this->buildJoinTable($data ?: [], Family::class, Student::class, 'family_id', 'student_id', 'addStudent', $manager);

        $data = Yaml::parse(file_get_contents(__DIR__. '/../DataFixtures/SQL/App/family_phone.yml'));

        $this->buildJoinTable($data ?: [], Family::class, Phone::class, 'family_id', 'phone_id', 'addPhone', $manager);

        $data = Yaml::parse(file_get_contents(__DIR__. '/../DataFixtures/SQL/App/person_phone.yml'));

        $this->buildJoinTable($data ?: [], Person::class, Phone::class, 'person_id', 'phone_id', 'addPhone', $manager);
    }

    /**
     * getDependencies
     *
     * @return array
     */
    public function getDependencies()
    {
        return [
            CalendarFixtures::class,
            SchoolFixtures::class,
            UserFixtures::class,
        ];
    }
}