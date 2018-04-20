<?php
namespace App\People\Entity;

use App\Entity\Activity;
use App\Entity\Calendar;
use App\Entity\FaceToFace;
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

    /**
     * @var string
     */
	private $activityList;

    /**
     * @return string
     */
    public function getActivityList(): string
    {
        $this->activityList = $this->activityList ?: '';
        return $this->activityList;
    }

    /**
     * @param string $activityList
     * @return StudentExtension
     */
    public function setActivityList(string $activityList): StudentExtension
    {
        $this->activityList = $activityList;
        return $this;
    }

    /**
     * @param Activity $activity
     * @return StudentExtension
     */
    public function addActivityToList(Activity $activity): StudentExtension
    {
        if (mb_strpos($this->getActivityList(), $activity->getFullName()) !== false)
            return $this;
        $this->activityList = $this->getActivityList() . ', ' . $activity->getFullName();
        $this->activityList = ltrim(', ', $this->activityList);
        return $this;
    }
}