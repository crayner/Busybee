<?php
namespace App\Calendar\Util;

use App\Entity\Activity;
use App\Entity\Calendar;
use App\Entity\CalendarGrade;

class CalendarGradeManager
{
    /**
     * @var $calendarManager
     */
    private $calendarManager;

    /**
     * CalendarGradeManager constructor.
     *
     * @param CalendarManager $calendarManager
     */
    public function __construct(CalendarManager $calendarManager)
    {
        $this->calendarManager = $calendarManager;
    }

    /**
     * @return int
     */
    public function getStudentCount(): int
    {
        return 0;
    }

    /**
     * @param Calendar $calendar
     * @return Calendar|null
     */
    public function getNextCalendar(Calendar $calendar): ?Calendar
    {
        return $this->calendarManager->getNextCalendar($calendar);
    }

    public function canDelete(CalendarGrade $calendarGrade): bool
    {
        if ($calendarGrade->canDelete()) {
            $em = $this->calendarManager->getEntityManager();

            $act = $em->getRepository(Activity::class)->createQueryBuilder('a')
                ->leftJoin('a.calendarGrades', 'g')
                ->where('g.id = :grade_id')
                ->setParameter('grade_id', $calendarGrade->getId())
                ->getQuery()
                ->getResult();
            if (! empty($act)) return false;
        } else
            return false;

        return true;

    }
}