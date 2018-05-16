<?php
namespace App\People\Entity;

use App\Calendar\Util\CalendarManager;
use App\Entity\Activity;
use App\Entity\Calendar;
use App\Entity\CalendarGrade;
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
     * @var array 
     */
	private static $statusList  = [
            'active' => [
                'future',
                'current',
            ],
            'inactive' => [
                'past',
                'archived',
                'left',
            ],
        ];

    public static function getStatusList(string $limit = ''): array
    {
        if (! in_array(strtolower($limit), ['active','inactive','']))
            throw new \InvalidArgumentException('Dear Programmer: The list accepts only "active, inactive or an empty string" for Status List');

        if ($limit === 'active')
            return self::$statusList['active'];
        if ($limit === 'inactive')
            return self::$statusList['inactive'];
        return self::$statusList;
    }

    /**
     * @var CalendarGrade|null
     */
    private $currentGrade;

    /**
     * @return null|CalendarGrade
     */
    public function getGradeInCurrentGrade(): ?CalendarGrade
    {
        if ($this->currentGrade)
            return $this->currentGrade;
        return $this->currentGrade = CalendarManager::getStudentGradeInCurrentCalendar($this);
    }
}