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
use App\Entity\CareGiver;
use App\Entity\DepartmentMember;
use App\Entity\Family;
use App\Entity\Locality;
use App\Entity\Person;
use App\Entity\Phone;
use App\Entity\Student;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Yaml\Yaml;

class PeopleFixtures extends Fixture implements DependentFixtureInterface
{
    use buildTable;

    /**
     * @var array
     */
    private $assoc = [
        'user_id' => 'user',
        'address_1' => 'address1',
        'address_2' => 'address2',
        'phone' => 'phone',
        'created_by'   => 'createdBy',
        'modified_by' => 'modifiedBy',
        'person_id' => 'person',
        'dept_id' => 'department',
        'staff_id' => 'staff',
        'student_id' => 'student',
        'calendar_grade_id' => 'calendarGrade',
        'locality_id' => 'locality',
    ];

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $data = Yaml::parse(file_get_contents('/SQL/App/locality.yml'));

        $this->buildTable($data, Locality::class, $manager);

        $data = Yaml::parse(file_get_contents('/SQL/App/address.yml'));

        $this->buildTable($data, Address::class, $manager);

        $data = Yaml::parse(file_get_contents('/SQL/App/people.yml'));

        $this->buildTable($data, Person::class, $manager);

        $data = Yaml::parse(file_get_contents('/SQL/App/family.yml'));

        $this->buildTable($data, Family::class, $manager);

        $data = Yaml::parse(file_get_contents('/SQL/App/caregiver.yml'));

        $this->buildTable($data, CareGiver::class, $manager);

        $data = Yaml::parse(file_get_contents(__DIR__. '/../DataFixtures/SQL/App/family_student.yml'));

        $this->buildJoinTable($data ?: [], Family::class, Student::class, 'family_id', 'student_id', 'addStudent', $manager);

        $data = Yaml::parse(file_get_contents(__DIR__. '/../DataFixtures/SQL/App/family_phone.yml'));

        $this->buildJoinTable($data ?: [], Family::class, Phone::class, 'family_id', 'phone_id', 'addPhone', $manager);

        $data = Yaml::parse(file_get_contents(__DIR__. '/../DataFixtures/SQL/App/person_phone.yml'));

        $this->buildJoinTable($data ?: [], Person::class, Phone::class, 'family_id', 'phone_id', 'addPhone', $manager);

        $data = Yaml::parse(file_get_contents('/SQL/App/department_member.yml'));

        $this->buildTable($data, DepartmentMember::class, $manager);

        $data = Yaml::parse(file_get_contents('/SQL/App/calendar_grade_student.yml'));

        $this->buildTable($data, DepartmentMember::class, $manager);
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
        ];
    }
}