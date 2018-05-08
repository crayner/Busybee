<?php
namespace App\People\Util;

use App\Address\Util\AddressManager;
use App\Calendar\Util\CalendarManager;
use App\Core\Manager\MessageManager;
use App\Core\Manager\SettingManager;
use App\Core\Manager\TabManagerInterface;
use App\Core\Util\UserManager;
use App\Entity\Calendar;
use App\Entity\CalendarGrade;
use App\Entity\CalendarGradeStudent;
use App\Entity\CareGiver;
use App\Entity\Family;
use App\Entity\Person;
use App\Entity\Staff;
use App\Entity\Student;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Hillrange\Security\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Yaml\Yaml;

class PersonManager implements TabManagerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var AddressManager
     */
    private $addressManager;

    /**
     * @var Person
     */
    private $person;

    /**
     * @var SettingManager
     */
    private $settingManager;

    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @var CalendarManager
     */
    private $calendarManager;

    /**
     * @var MessageManager
     */
    private $messageManager;

    /**
     * PersonManager constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, AddressManager $addressManager, SettingManager $settingManager, UserManager $userManager, CalendarManager $calendarManager)
    {
        $this->entityManager = $entityManager;
        $this->addressManager = $addressManager;
        $this->settingManager = $settingManager;
        $this->userManager = $userManager;
        $this->calendarManager = $calendarManager;
        $this->messageManager = $calendarManager->getMessageManager();
    }

    /**
     * @param null|string|integer $id
     *
     * @return Person|null
     */
    public function getPerson($id = null)
    {
        if (!is_null($id))
            $this->person = $this->find($id);

        return $this->person;
    }

    /**
     * @param $id
     *
     * @return Person|null
     */
    public function find($id): ?Person
    {
        $this->person = new Person();

        if ($id !== 'Add')
            $this->person = $this->entityManager->getRepository(Person::class)->find($id);

        return $this->person;
    }

    /**
     * @return array
     */
    public function getTabs(): array
    {
        return Yaml::parse("
person:
    label: person.person.tab
    include: Person/person.html.twig
    message: personMessage
    translation: Person
contact:
    label: person.contact.tab
    include: Person/contact.html.twig
    translation: Person
staff:
    label: person.staff.tab
    include: Person/staff.html.twig
    message: staffMessage
    translation: Person
    display: isStaff
student:
    label: person.student.tab
    include: Person/student.html.twig
    message: studentMessage
    translation: Person
    display: isStudent
immigration:
    label: person.immigration.tab
    include: Person/immigration.html.twig
    message: immigrationMessage
    translation: Person
    display: isStudent
calendar_grade:
    label: student.calendar_grade.tab
    include: Student/calendar_grade.html.twig
    message: calendarGradeMessage
    translation: Person
    display: isStudent
user:
    label: person.user.tab
    include: Person/user.html.twig
    message: userMessage
    translation: Person
    display: isUser
");
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * @return AddressManager
     */
    public function getAddressManager(): AddressManager
    {
        return $this->addressManager;
    }

    /**
     * @param Person $person
     *
     * @return ArrayCollection
     */
    public function getAddresses(Person $person): ArrayCollection
    {
        $families = $this->getFamilies($person);

        $addresses = new ArrayCollection();
        foreach ($families as $family) {
            $address = $family->getAddress1();
            if (!is_null($address) && !$addresses->contains($address))
                $addresses->add($address);
            $address = $family->getAddress2();
            if (!is_null($address) && !$addresses->contains($address))
                $addresses->add($address);
        }

        return $addresses;
    }

    /**
     * @param Person $person
     *
     * @return ArrayCollection
     */
    public function getFamilies(Person $person = null): ArrayCollection
    {
        $this->checkPerson($person);

        $families = new ArrayCollection();
        if ($this->isFamilyInstalled()) {
            $careGivers = $this->entityManager->getRepository(Family::class)->findByCareGiverPerson($this->person);
            if (!empty($careGivers))
                foreach ($careGivers as $family)
                    if (!$families->contains($family))
                        $families->add($family);

            $students = $this->entityManager->getRepository(Family::class)->findByStudentsPerson($this->person);
            if (!empty($students))
                foreach ($students as $family)
                    if (!$families->contains($family))
                        $families->add($family);
        }

        return $families;
    }

    /**
     * @param Person|null $person
     *
     * @return Person
     */
    private function checkPerson(Person $person = null): Person
    {
        if ($person instanceof Person) {
            $this->person = $person;

            return $this->person;
        }

        if ($this->person instanceof Person)
            return $this->person;

        $this->person = $this->getPerson('Add');

        return $this->person;
    }

    /**
     * @return bool
     */
    public function isFamilyInstalled(): bool
    {
        if (class_exists('App\Entity\Family')) {
            $metaData = $this->getEntityManager()->getClassMetadata('App\Entity\Family');
            $schema = $this->getEntityManager()->getConnection()->getSchemaManager();

            return $schema->tablesExist([$metaData->table['name']]);

        }

        return false;
    }

    /**
     * @param Person $person
     *
     * @param bool $all
     *
     * @return ArrayCollection
     */
    public function getPhones(Person $person, $all = false): ArrayCollection
    {
        $families = $this->getFamilies($person);
        $phones = new ArrayCollection();

        foreach ($families as $family) {
            foreach ($family->getPhone() as $phone)
                if (!$phones->contains($phone)) $phones->add($phone);
        }

        if ($all)
            foreach ($person->getPhone() as $phone)
                if (!$phones->contains($phone)) $phones->add($phone);

        return $phones;
    }

    /**
     * @param Person $person
     *
     * @return bool
     */
    public function isStaff(Person $person = null): bool
    {
        $this->checkPerson($person);

        if ($this->person instanceof Staff)
            return true;

        return false;
    }

    /**
     * @param Person|null $person
     *
     * @return bool
     */
    public function isStudent(Person $person = null): bool
    {
        $this->checkPerson($person);

        if ($this->person instanceof Student)
            return true;

        return false;
    }

    /**
     * @param Person $person
     *
     * @return bool
     */
    public function isUser(Person $person = null)
    {
        $person = $this->checkPerson($person);
        if (is_null($person->getUser()))
            return false;

        $user = $this->entityManager->getRepository(User::class)->find($person->getUser()->getId());
        if ($user instanceof User)
            return true;

        return false;
    }

    /**
     * @param Person|null $person
     *
     * @return bool
     */
    public function validPerson(Person $person = null)
    {
        $person = $this->checkPerson($person);

        if ($person instanceof Person && intval($person->getId()) > 0)
            return true;

        return false;
    }

    /**
     * @param Person $person
     *
     * @return bool
     */
    public function canDeleteStaff(Person $person = null)
    {
        $this->checkPerson($person);

        if (!$this->person instanceof Staff)
            return false;

        return $this->person->canDelete();
    }

    /**
     * @param Person $person
     *
     * @return bool
     */
    public function canBeStaff(Person $person = null): bool
    {
        $this->checkPerson($person);
        //place rules here to stop new staff.
        if ($this->isStudent())
            return false;

        return true;
    }

    /**
     * @param   Person $person
     * @param   array $parameters
     *
     * @return  bool
     */
    public function canDeleteStudent(Person $person = null, $parameters = []): bool
    {
        $student = $this->checkPerson($person);

        //Place rules here to stop delete .
        if (!$student instanceof Student)
            return false;

        $families = $this->getFamilies($student);

        if (is_array($families) && count($families) > 0)
            return false;
        if ($this->isGradesInstalled()) {
            $grades = $this->entityManager->getRepository(RollGroup::class)->findAll(['status' => 'Current', 'student' => $student->getId()]);

            if (is_array($grades) && count($grades) > 0)
                return false;
        }

        if (is_array($parameters))
            foreach ($parameters as $data)
                if (isset($data['data']['name']) && isset($data['entity']['name'])) {
                    $client = $this->entityManager->getRepository($data['entity']['name'])->findOneByStudent($student->getId());

                    if (is_object($client) && $client->getId() > 0)
                        return false;
                }

        return $student->canDelete();
    }

    /**
     * @param Person $person
     *
     * @return bool
     */
    public function canBeStudent(Person $person = null): bool
    {
        $this->checkPerson($person);
        //place rules here to stop new student.

        if ($this->isStaff() || $this->isCareGiver())
            return false;

        return true;
    }

    /**
     * @param Person $person
     *
     * @return bool
     */
    public function isCareGiver(Person $person = null): bool
    {
        $person = $this->checkPerson($person);

        if (!$this->entityManager->getMetadataFactory()->hasMetadataFor(CareGiver::class))
            return false;

        $careGiver = $this->entityManager->getRepository(CareGiver::class)->findOneByPerson($person);
        if ($careGiver instanceof CareGiver)
            return true;

        return false;
    }

    /**
     * @param Person $person
     *
     * @return bool
     */
    public function canDeleteUser(Person $person = null): bool
    {
        $person = $this->checkPerson($person);

        if ($person->getUser() instanceof UserInterface)
            return $person->getUser()->canDelete();

        return false;
    }

    /**
     * @param null|Person $person
     *
     * @return bool
     */
    public function canBeUser(Person $person = null)
    {
        $person = $this->checkPerson($person);
        if (empty($person->getEmail()))
            return false;

        return true;
    }

    /**
     * Get Details
     * @param Person $person
     * @return string
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getDetails(Person $person): string
    {
        $result = '';

        if ($person instanceof Staff && !empty($person->getHonorific()))
            $result .= $person->getHonorific() . '<br/>';

        if ($person instanceof Student) {
            $families = $this->entityManager->createQueryBuilder()
                ->from(Family::class, 'f')
                ->select('f')
                ->innerJoin('f.students', 's', 'WITH', 's.id = :student_id')
                ->setParameter('student_id', $person->getId())
                ->getQuery()
                ->getResult();
            foreach ($families as $family)
                $result .= 'Family: ' . $family->getName() . '<br />';

            $grade = $this->getEntityManager()->getRepository(CalendarGrade::class)->createQueryBuilder('cg')
                ->leftJoin('cg.students', 'cgs')
                ->leftJoin('cgs.student', 's')
                ->leftJoin('cg.calendar', 'c')
                ->where('s = :student')
                ->setParameter('student', $person)
                ->andWhere('cg.calendar = :calendar')
                ->setParameter('calendar', $this->calendarManager->getCurrentCalendar())
                ->getQuery()
                ->getOneOrNullResult();

            if ($grade instanceof CalendarGrade)
                $result .= $grade->getFullName() . '<br />';

        }

        if (!$person instanceof Staff && !$person instanceof Student) {
            if ($caregiver = $this->entityManager->getRepository(CareGiver::class)->findOneByPerson($person))
                $result .= 'Family: ' . $caregiver->getFamily()->getName() . '<br />';
            else
                $result .= 'Contact<br />';

        }

        if (!empty($person->getEmail()))
            $result .= $person->getEmail() . '<br/>';

        if (!empty($person->getEmail2()))
            $result .= $person->getEmail2() . '<br/>';

        foreach ($this->getPhones($person, true) as $phone) {
            $x = $this->getSettingManager()->get('phone.display', null, ['phone' => $phone->getPhoneNumber()]);
            if (!empty($x))
                $result .= str_replace("\n", "<br/>", $x);
        }

        return $result;
    }

    /**
     * Create Staff
     * @param Person|null $person
     * @param bool $persist
     * @return bool
     * @throws \Doctrine\DBAL\DBALException
     */
    public function createStaff(Person $person = null, $persist = false): bool
    {
        $this->checkPerson($person);

        if ($this->canBeStaff()) {
            $tableName = $this->entityManager->getClassMetadata(Person::class)->getTableName();

            $this->entityManager->getConnection()->exec('UPDATE `' . $tableName . '` SET `person_type` = "staff" WHERE `' . $tableName . '`.`id` = ' . $this->person->getId());

            if ($persist) {
                $this->entityManager->persist($this->person);
                $this->entityManager->flush();
            }

            $this->entityManager->refresh($this->person);

            return true;
        }

        return false;
    }

    /**
     * Create Staff
     * @param Person|null $person
     * @throws \Doctrine\DBAL\DBALException
     */
    public function deleteStaff(Person $person = null)
    {
        $this->checkPerson($person);

        if ($this->canDeleteStaff()) {
            $this->switchToPerson();
        }
    }

    /**
     * Switch to Person
     * @param Person|null $person
     * @param bool $persist
     * @return Person|null
     * @throws \Doctrine\DBAL\DBALException
     */
    public function switchToPerson(Person $person = null, $persist = false): ?Person
    {
        $this->checkPerson($person);

        if (is_subclass_of($this->person, Person::class)) {
            $tableName = $this->entityManager->getClassMetadata(Person::class)->getTableName();

            $x = $this->entityManager->getConnection()->exec('UPDATE `' . $tableName . '` SET `person_type` = "person" WHERE `' . $tableName . '`.`id` = ' . $this->person->getId());

            if ($persist) {
                $this->entityManager->persist($this->person);
                $this->entityManager->flush();
            }

            $this->entityManager->refresh($this->person);

        }

        return $this->person;
    }

    /**
     * Create Student
     * @param Person|null $person
     * @param bool $persist
     * @return bool
     * @throws \Doctrine\DBAL\DBALException
     */
    public function createStudent(Person $person = null, $persist = false): bool
    {
        $this->checkPerson($person);

        if ($this->canBeStudent()) {
            $this->switchToStudent();

            return true;
        }

        return false;
    }

    /**
     * Switch to Student
     * @param Person|null $person
     * @param bool $persist
     * @return Student|null
     * @throws \Doctrine\DBAL\DBALException
     */
    public function switchToStudent(Person $person = null, $persist = false): ?Student
    {
        $this->checkPerson($person);

        if ($this->person instanceof Person && !is_subclass_of($this->person, Person::class)) {
            $tableName = $this->entityManager->getClassMetadata(Person::class)->getTableName();

            $x = $this->entityManager->getConnection()->exec('UPDATE `' . $tableName . '` SET `person_type` = "student" WHERE `' . $tableName . '`.`id` = ' . strval(intval($this->person->getId())));

            if ($persist) {
                $this->entityManager->persist($this->person);
                $this->entityManager->flush();
            }

            $this->person = $this->entityManager->getRepository(Student::class)->find($this->person->getId());

        }

        return $this->person;
    }

    /**
     * @return bool
     */
    public function isGradesInstalled(): bool
    {
        if (class_exists('App\Entity\RollGroup')) {
            $metaData = $this->getEntityManager()->getClassMetadata('App\Entity\RollGroup');
            $schema = $this->getEntityManager()->getConnection()->getSchemaManager();

            return $schema->tablesExist([$metaData->table['name']]);

        }

        return false;
    }

    /**
     * @param UserInterface $current
     * @param UserInterface $user
     *
     * @return bool
     */
    public function canImpersonateUser(UserInterface $current, UserInterface $user)
    {
        if ($current->isEqualTo($user))
            return false;

        if ($user->isCredentialsExpired())
            return false;

        if ($user->isLocked())
            return false;

        return true;
    }

    /**
     * Create Staff
     * @param Person|null $person
     * @throws \Doctrine\DBAL\DBALException
     */
    public function deleteStudent(Person $person = null)
    {
        $this->checkPerson($person);

        if ($this->canDeleteStudent()) {
            $this->switchToPerson();
        }
    }

    /**
     * @return SettingManager
     */
    public function getSettingManager(): SettingManager
    {
        return $this->settingManager;
    }

    /**
     * @return Calendar
     */
    public function getCurrentCalendar(): Calendar
    {
        return $this->userManager->getCurrentCalendar();
    }

    /**
     * Switch to Staff
     * @param Person|null $person
     * @param bool $persist
     * @return Staff|null
     * @throws \Doctrine\DBAL\DBALException
     */
    public function switchToStaff(Person $person = null, $persist = false): ?Staff
    {
        $this->checkPerson($person);

        if ($this->person instanceof Person && !is_subclass_of($this->person, Person::class)) {
            $tableName = $this->entityManager->getClassMetadata(Person::class)->getTableName();

            $x = $this->entityManager->getConnection()->exec('UPDATE `' . $tableName . '` SET `person_type` = "staff" WHERE `' . $tableName . '`.`id` = ' . strval(intval($this->person->getId())));

            if ($persist) {
                $this->entityManager->persist($this->person);
                $this->entityManager->flush();
            }

            $this->entityManager->refresh($this->person);

        }

        return $this->person;
    }

    /**
     * @return CalendarManager
     */
    public function getCalendarManager(): CalendarManager
    {
        return $this->calendarManager;
    }

    /**
     * @return string
     */
    public function getResetScripts(): string
    {
        return '';
    }

    /**
     * @param string $method
     * @return bool
     */
    public function isDisplay(string $method = ''): bool
    {
        if ($method === '')
            return true;
        if (method_exists($this, $method))
            return (bool)$this->$method();
        return false;
    }

    /**
     * @return MessageManager
     */
    public function getMessageManager(): MessageManager
    {
        return $this->messageManager;
    }
}