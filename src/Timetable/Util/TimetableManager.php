<?php
namespace App\Timetable\Util;

use App\Calendar\Util\CalendarManager;
use App\Entity\Timetable;
use Doctrine\ORM\EntityManagerInterface;

class TimetableManager
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var CalendarManager
     */
    private $calendarManager;

    /**
     * TimetableManager constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, CalendarManager $calendarManager)
    {
        $this->entityManager = $entityManager;
        $this->calendarManager = $calendarManager;
    }

    /**
     * @var null|Timetable
     */
    private $timetable;

    /**
     * @param $id
     */
    public function find($id): Timetable
    {
        $this->setTimetable($this->entityManager->getRepository(Timetable::class)->find($id));
        return $this->getTimetable(true);
    }

    /**
     * @return Timetable|null
     */
    public function getTimetable(bool $notNull = false): ?Timetable
    {
        if ($notNull && is_null($this->timetable))
        {
            $this->setTimetable(new Timetable());
            $this->timetable->setCalendar($this->calendarManager->getCurrentCalendar());
        }

        return $this->timetable;
    }

    /**
     * @param Timetable|null $timetable
     * @return TimetableManager
     */
    public function setTimetable(?Timetable $timetable): TimetableManager
    {
        $this->timetable = $timetable;

        return $this;
    }

    /**
     * @param Timetable $tt
     * @return string
     */
    public function displayGrades(Timetable $tt): string
    {
        $result = '';
        foreach($tt->getCalendarGrades()->getIterator() as $grade)
            $result .= $grade->getGrade() . ', ';

        return trim($result, ', ');
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }
}