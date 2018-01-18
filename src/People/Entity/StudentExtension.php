<?php
namespace App\People\Entity;

use App\Entity\Calendar;
use App\Entity\Person;

abstract class StudentExtension extends Person
{
	/**
	 * @var Calendar
	 */
	private $calendar;

	/**
	 * @todo Add Student Delete checks.
	 * @return bool
	 */
	public function canDelete()
	{
		return parent::canDelete();
	}

	/**
	 * @param Calendar $calendar
	 */
	public function getStudentCalendarGroup(Calendar $calendar)
	{
		$grades = $this->getGrades();

		foreach ($grades as $grade)
		{
			if ($grade->getGrade()->getCalendar()->getId() == $calendar->getId())
				return $grade->getGrade();
		}

		return null;
	}

	/**
	 * @return Calendar
	 */
	public function getCalendar(): Calendar
	{
		return $this->calendar;
	}

	/**
	 * @param Calendar $calendar
	 */
	public function setCalendar(Calendar $calendar)
	{
		$this->calendar = $calendar;

		return $this;
	}
}