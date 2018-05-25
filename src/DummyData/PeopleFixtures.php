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
use Psr\Log\LoggerInterface;
use Symfony\Component\Yaml\Yaml;

class PeopleFixtures implements DummyDataInterface
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
        $data = Yaml::parse(file_get_contents(__DIR__ . '/SQL/App/locality.yml'));

        $this->setLogger($logger)->buildTable($data ?: [], Locality::class, $manager);

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

        $data = Yaml::parse(file_get_contents(__DIR__. '/../DummyData/SQL/App/family_student.yml'));

        $this->buildJoinTable($data ?: [], Family::class, Student::class, 'family_id', 'student_id', 'addStudent', $manager);

        $data = Yaml::parse(file_get_contents(__DIR__. '/../DummyData/SQL/App/family_phone.yml'));

        $this->buildJoinTable($data ?: [], Family::class, Phone::class, 'family_id', 'phone_id', 'addPhone', $manager);

        $data = Yaml::parse(file_get_contents(__DIR__. '/../DummyData/SQL/App/person_phone.yml'));

        $this->buildJoinTable($data ?: [], Person::class, Phone::class, 'person_id', 'phone_id', 'addPhone', $manager);
    }
}