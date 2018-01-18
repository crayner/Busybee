<?php
namespace App\Entity;

use App\Entity\CalendarGroup;
use Hillrange\Security\Entity\User;
use App\People\Entity\StudentCalendarGroupExtension;

/**
 * Student Calendar Group
 */
class StudentCalendarGroup extends StudentCalendarGroupExtension
{
	/**
	 * @var integer
	 */
	private $id;

	/**
	 * @var string
	 */
	private $status;

	/**
	 * @var Student
	 */
	private $student;

	/**
	 * @var CalendarGroup
	 */
	private $calendarGroup;

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
	 * Get status
	 *
	 * @return string
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * Set status
	 *
	 * @param string $status
	 *
	 * @return StudentCalendarGroup
	 */
	public function setStatus($status): StudentCalendarGroup
	{
		$this->status = $status;

		return $this;
	}

	/**
	 * Get calendarGroup
	 *
	 * @return CalendarGroup
	 */
	public function getCalendarGroup()
	{
		return $this->calendarGroup;
	}

	/**
	 * Set calendarGroup
	 *
	 * @param CalendarGroup $calendarGroup
	 *
	 * @return StudentCalendarGroup
	 */
	public function setCalendarGroup(CalendarGroup $calendarGroup = null, $add = true): StudentCalendarGroup
	{
		if ($add)
			$calendarGroup->addStudent($this, false);

		$this->calendarGroup = $calendarGroup;

		return $this;
	}

	/**
	 * Get Student
	 *
	 * @return Student|null
	 */
	public function getStudent()
	{
		return $this->student;
	}

	/**
	 * Set Student
	 *
	 * @param Student $student
	 */
	public function setStudent(Student $student): StudentCalendarGroup
	{
		$this->student = $student;

		return $this;
	}
}
