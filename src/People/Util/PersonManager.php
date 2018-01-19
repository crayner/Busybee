<?php
namespace App\People\Util;

use App\Address\Util\AddressManager;
use App\Core\Util\UserManager;
use App\Entity\CareGiver;
use App\Entity\Person;
use App\Entity\Staff;
use App\Entity\Student;
use App\Entity\StudentCalendarGroup;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Hillrange\Security\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Yaml\Yaml;

class PersonManager
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
	 * PersonManager constructor.
	 *
	 * @param EntityManagerInterface $entityManager
	 */
	public function __construct(EntityManagerInterface $entityManager, AddressManager $addressManager)
	{
		$this->entityManager = $entityManager;
		$this->addressManager = $addressManager;
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
contact:
    label: person.contact.tab
    include: Person/contact.html.twig
staff:
    label: person.staff.tab
    include: Person/staff.html.twig
    message: staffMessage
    translation: Person
immigration:
    label: person.immigration.tab
    include: Person/immigration.html.twig
    message: immigrationMessage
    translation: Person
student:
    label: person.student.tab
    include: Person/student.html.twig
    message: studentMessage
    translation: Person
grades:
    label: person.grades.tab
    include: Person/gradeStart.html.twig
    message: gradeMessage
    translation: Person
user:
    label: person.user.tab
    include: Person/user.html.twig
    message: userMessage
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
		foreach ($families as $family)
		{
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
		if ($this->isFamilyInstalled())
		{
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
		if ($person instanceof Person)
		{
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
		if (class_exists('Busybee\People\FamilyBundle\Model\FamilyManager'))
		{
			$metaData = $this->getOm()->getClassMetadata('Busybee\People\FamilyBundle\Entity\Family');
			$schema   = $this->getOm()->getConnection()->getSchemaManager();

			return $schema->tablesExist([$metaData->table['name']]);

		}

		return false;
	}

	/**
	 * @param Person $person
	 *
	 * @param bool   $all
	 *
	 * @return ArrayCollection
	 */
	public function getPhones(Person $person, $all = false): ArrayCollection
	{
		$families = $this->getFamilies($person);
		$phones   = new ArrayCollection();

		foreach ($families as $family)
		{
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
	 * @param   array  $parameters
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
		if ($this->gradesInstalled())
		{
			$grades = $this->entityManager->getRepository(StudentCalendarGroup::class)->findAll(['status' => 'Current', 'student' => $student->getId()]);

			if (is_array($grades) && count($grades) > 0)
				return false;
		}

		if (is_array($parameters))
			foreach ($parameters as $data)
				if (isset($data['data']['name']) && isset($data['entity']['name']))
				{
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
	 *
	 * @param Person $person
	 *
	 * @return string
	 */
	public function getDetails(Person $person)
	{
		$result = '';

		if ($person instanceof Staff && !empty($person->getHonorific()))
			$result .= $person->getHonorific() . '<br/>';

		if ($person instanceof Student && !empty($this->getCurrentGrade($person)))
			$result .= $this->getCurrentGrade($person) . '<br/>';

		if (!empty($person->getEmail()))
			$result .= $person->getEmail() . '<br/>';
		if (!empty($person->getEmail2()))
			$result .= $person->getEmail2() . '<br/>';

		foreach ($this->getPhones($person, true) as $phone)
		{
			$x = $this->getSm()->get('phone.display', null, ['phone' => $phone->getPhoneNumber()]);
			if (!empty($x))
				$result .= str_replace("\n", "<br/>", $x);
		}

		return $result;
	}
}