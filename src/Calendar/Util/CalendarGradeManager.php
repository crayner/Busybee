<?php
namespace App\Calendar\Util;

use App\Core\Manager\SettingManager;
use App\Entity\Activity;
use App\Entity\Calendar;
use App\Entity\CalendarGrade;

class CalendarGradeManager
{
    /**
     * @var CalendarManager
     */
    private $calendarManager;

    /**
     * CalendarGradeManager constructor.
     * @param SettingManager $settingManager
     */
    public function __construct(SettingManager $settingManager)
    {
        $this->settingManager = $settingManager;
    }

    /**
     * @return int
     */
    public function getStudentCount(CalendarGrade $calendarGrade = null): int
    {
        if ($calendarGrade instanceof CalendarGrade)
            return $calendarGrade->getStudents()->count();
        return 0;
    }

    /**
     * @param Calendar $calendar
     * @return Calendar|null
     */
    public function getNextCalendar(Calendar $calendar): ?Calendar
    {
        return $this->getCalendarManager()->getNextCalendar($calendar);
    }

    /**
     * @param CalendarGrade|null $calendarGrade
     * @return bool
     */
    public function canDelete(CalendarGrade $calendarGrade = null): bool
    {
        if(! $calendarGrade instanceof CalendarGrade)
            return true;

        if ($calendarGrade->canDelete()) {
            $em = $this->getCalendarManager()->getEntityManager();

            $act = $em->getRepository(Activity::class)->createQueryBuilder('a')
                ->leftJoin('a.calendarGrades', 'g')
                ->where('g.id = :grade_id')
                ->setParameter('grade_id', $calendarGrade->getId())
                ->getQuery()
                ->getResult();
            if (! empty($act))
                return false;

            if ($this->getStudentCount($calendarGrade) > 0)
                return false;

        } else
            return false;

        return true;

    }

    /**
     * @param CalendarGrade|null $calendarGrade
     * @return string
     */
    public function getFullName(CalendarGrade $calendarGrade = null): string
    {
        if ($calendarGrade instanceof CalendarGrade)
        {
            $name = '';
            $name = $calendarGrade->getGrade();
            $grades = $this->settingManager->get('student.groups._flip');
            $name = $grades[$name] ?: $name;
            if ($calendarGrade->getCalendar() instanceof Calendar)
            {
                $name .= ' (' . $calendarGrade->getCalendar()->getName() . ')';
            }
            return $name;
        }
        return '';
    }

    /**
     * @return CalendarManager
     */
    public function getCalendarManager(): CalendarManager
    {
        return $this->calendarManager;
    }

    /**
     * @param CalendarManager $calendarManager
     * @return CalendarGradeManager
     */
    public function setCalendarManager(CalendarManager $calendarManager): CalendarGradeManager
    {
        $this->calendarManager = $calendarManager;

        return $this;
    }
}