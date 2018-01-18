<?php
namespace App\Entity;

use App\Calendar\Entity\CalendarGroupExtension;
use App\Entity\Space;
use App\Entity\Staff;
use App\Entity\StudentCalendarGroup;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Hillrange\Security\Util\UserTrackInterface;
use Hillrange\Security\Util\UserTrackTrait;

/**
 * Calendar Group
 */
class CalendarGroup extends CalendarGroupExtension implements UserTrackInterface
{
	use UserTrackTrait;
	/**
	 * @var integer
	 */
	private $id;

	/**
	 * @var string
	 */
	private $nameShort;

	/**
	 * @var Calendar
	 */
	private $calendar;

	/**
	 * @var integer
	 */
	private $sequence;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var ArrayCollection
	 */
	private $students;

	/**
	 * @var Staff
	 */
	private $calendarTutor;

	/**
	 * @var string
	 */
	private $website;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->students = new ArrayCollection();
	}

	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Get nameShort
	 *
	 * @return string
	 */
	public function getNameShort(): ?string
	{
		return $this->nameShort;
	}

	/**
	 * Set nameShort
	 *
	 * @param string $nameShort
	 *
	 * @return CalendarGroup
	 */
	public function setNameShort($nameShort): CalendarGroup
	{
		$this->nameShort = $nameShort;

		return $this;
	}

	/**
	 * Get calendar
	 *
	 * @return Calendar
	 */
	public function getCalendar()
	{
		return $this->calendar;
	}

	/**
	 * Set calendar
	 *
	 * @param Calendar $calendar
	 *
	 * @return CalendarGroup
	 */
	public function setCalendar(Calendar $calendar = null)
	{
		$this->calendar = $calendar;

		return $this;
	}

	/**
	 * Get sequence
	 *
	 * @return integer
	 */
	public function getSequence()
	{
		return $this->sequence;
	}

	/**
	 * Set sequence
	 *
	 * @param integer $sequence
	 *
	 * @return CalendarGroup
	 */
	public function setSequence($sequence)
	{
		$this->sequence = $sequence;

		return $this;
	}

	/**
	 * Get name
	 *
	 * @return string
	 */
	public function getName()
	{
		if (empty($this->name))
			return $this->nameShort;

		return $this->name;
	}

	/**
	 * Set name
	 *
	 * @param string $name
	 *
	 * @return CalendarGroup
	 */
	public function setName($name)
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * Get Students
	 *
	 * @return Collection
	 */
	public function getStudents()
	{
		return $this->students;
	}

	/**
	 * Set Students
	 *
	 * @param ArrayCollection $students
	 *
	 * @return CalendarGroup
	 */
	public function setStudents(ArrayCollection $students): CalendarGroup
	{
		$this->students = $students;

		return $this;
	}

	/**
	 * Add Student
	 *
	 * @param StudentCalendarGroup|null $student
	 *
	 * @return CalendarGroup
	 */
	public function addStudent(StudentCalendarGroup $student = null, $add = true): CalendarGroup
	{
		if (!$student instanceof StudentCalendarGroup)
			return $this;

		if ($add)
			$student->setCalendarGroup($this, false);

		if (!$this->students->contains($student))
			$this->students->add($student);

		return $this;
	}

	/**
	 * Remove Student
	 *
	 * @param StudentCalendarGroup $student
	 *
	 * @return CalendarGroup
	 */
	public function removeStudent(StudentCalendarGroup $student): CalendarGroup
	{
		$this->students->removeElement($student);

		return $this;
	}

	/**
	 * @return Staff|null
	 */
	public function getCalendarTutor(): ?Staff
	{
		return $this->calendarTutor;
	}

	/**
	 * @param Staff $CalendarTutor
	 *
	 * @return CalendarGroup
	 */
	public function setCalendarTutor(Staff $CalendarTutor = null): CalendarGroup
	{
		$this->calendarTutor = $CalendarTutor;

		return $this;
	}
	/**
	 * @return string|null
	 */
	public function getWebsite(): ?string
	{
		return $this->website;
	}

	/**
	 * @param string|null $website
	 *
	 * @return CalendarGroup
	 */
	public function setWebsite(string $website = null): CalendarGroup
	{
		$this->website = $website;

		return $this;
	}

}
