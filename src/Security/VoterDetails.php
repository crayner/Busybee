<?php
namespace App\Security;

use App\Core\Util\UserManager;
use App\Entity\Activity;
use App\Entity\Person;
use App\Entity\Staff;
use App\Entity\Student;
use App\Entity\TimetablePeriodActivity;
use App\People\Util\PersonManager;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Hillrange\Security\Entity\User;

class VoterDetails
{
	/**
	 * @var ArrayCollection
	 */
	private $grades;

	/**
	 * @var Student
	 */
	private $student;

	/**
	 * @var Staff
	 */
	private $staff;

	/**
	 * @var Activity
	 */
	private $activity;

	/**
	 * @var string|null
	 */
	private $identifierType;

	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;

    /**
     * @var UserManager 
     */
	private $userManager;

	/**
	 * VoterDetails constructor.
	 */
	public function __construct(EntityManagerInterface $entityManager, UserManager $userManager)
	{
		$this->grades         = new ArrayCollection();
		$this->student        = null;
		$this->activity       = null;
		$this->staff          = null;
		$this->entityManager             = $entityManager;
		$this->identifierType = null;
        $this->userManager = $userManager;
	}

	public function parseIdentifier($identifier)
	{
		$this->addGrade($identifier)
			->addStudent($identifier)
			->addStaff($identifier)
			->setIdentifierType($identifier);

		return $this;
	}

	/**
	 * Add Staff
	 *
	 * @param $staff
	 *
	 * @return VoterDetails
	 */
	public function addStaff($staff): VoterDetails
	{
		if ($staff instanceof User)
			$staff = $staff->getPerson();

		if ($staff instanceof Person)
			$staff = $staff->getStaff();

		if ($staff instanceof Staff)
			return $this->setStaff($staff);

		if (substr($staff, 0, 4) !== 'staf')
			return $this->setStaff(null);

		$id = intval(substr($staff, 4));

		if (gettype($id) !== 'integer' || empty($id))
			return $this->setStaff(null);

		$staff = $this->entityManager->getRepository(Staff::class)->find($id);

		if ($staff instanceof Staff)
			return $this->setStaff($staff);

		return $this->setStaff(null);
	}

	/**
	 * Add Student
	 *
	 * @param int $id
	 *
	 * @return VoterDetails
	 */
	public function addStudent($student): VoterDetails
	{
		if (substr($student, 0, 4) !== 'stud')
			return $this->setStudent(null);

		$id = intval(substr($student, 4));

		if (gettype($id) !== 'integer' || empty($id))
			return $this->setStudent(null);

		$student = $this->entityManager->getRepository(Student::class)->find($id);
		if ($student instanceof Student)
			$this->setStudent($student);

		return $this;
	}

	/**
	 * Add Grade
	 *
	 * @param string $grade
	 *
	 * @return VoterDetails
	 */
	public function addGrade($grade): VoterDetails
	{
		if (substr($grade, 0, 4) !== 'grad')
			return $this;

		if ($this->grades->contains($grade))
			return $this;

		$this->grades->add($grade);

		return $this;
	}

	/**
	 * Remove Grade
	 *
	 * @param string $grade
	 *
	 * @return VoterDetails
	 */
	public function removeGrade($grade): VoterDetails
	{
		$this->grades->removeElement($grade);

		return $this;
	}

	/**
	 * @return ArrayCollection
	 */
	public function getGrades(): ArrayCollection
	{
		return $this->grades;
	}

	/**
	 * Get Student
	 *
	 * @return null
	 */
	public function getStudent()
	{
		return $this->student;
	}

	/**
	 * Set Student
	 *
	 * @param Student $student
	 *
	 * @return VoterDetails
	 */
	public function setStudent(Student $student = null): VoterDetails
	{
		$this->student = $student;

		return $this;
	}

	/**
	 * Get Student
	 *
	 * @return null
	 */
	public function getStaff()
	{
		return $this->staff;
	}

	/**
	 * Set Student
	 *
	 * @param Student $student
	 *
	 * @return VoterDetails
	 */
	public function setStaff(Staff $staff = null): VoterDetails
	{
		$this->staff = $staff;

		return $this;
	}

    /**
     * userIdentifier
     *
     * @param PersonManager $pm
     * @param UserManager $userManager
     * @return VoterDetails
     * @throws \Doctrine\ORM\ORMException
     */
    public function userIdentifier(PersonManager $pm): VoterDetails
	{
		$person = $this->getUserManager()->getPerson();

		if ($person instanceof Person)
		{
			if ($pm->isStaff($person))
			{
				$this->setIdentifierType('staff');

				return $this->addStaff('staf' . $person->getId());
			}
			if ($pm->isStudent($person))
			{
				$this->setIdentifierType('student');

				return $this->addStudent('stud' . $person->getId());
			}
		}

		return $this;
	}

	/**
	 * @param $id of PeriodActivity
	 *
	 * @return VoterDetails
	 */
	public function activityIdentifier($id): VoterDetails
	{
		$this->activity = $this->entityManager->getRepository(TimetablePeriodActivity::class)->find($id);

		return $this;
	}

	/**
	 * @return Activity|null
	 */
	public function getActivity()
	{
		return $this->activity;
	}

	/**
	 * @param Activity $activity
	 *
	 * @return VoterDetails
	 */
	public function setActivity(Activity $activity): VoterDetails
	{
		$this->activity = $activity;

		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getIdentifierType()
	{
		return $this->identifierType;
	}

	/**
	 * @param $identifier
	 *
	 * @return VoterDetails
	 */
	public function setIdentifierType($identifier): VoterDetails
	{
		$this->identifierType = substr($identifier, 0, 4);

		if (!in_array($this->identifierType, ['grad', 'staf', 'stud']))
			$this->identifierType = null;

		return $this;
	}

    /**
     * @return UserManager
     */
    public function getUserManager(): UserManager
    {
        return $this->userManager;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }
}
