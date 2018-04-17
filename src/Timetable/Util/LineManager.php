<?php
namespace App\Timetable\Util;

use App\Calendar\Util\CalendarManager;
use App\Entity\Calendar;
use App\Entity\Line;
use Doctrine\ORM\EntityManagerInterface;

class LineManager
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * Current Calednar
     * @var Calendar
     */
    private $calendar;

    /**
     * LineManager constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, CalendarManager $calendarManager)
    {
        $this->entityManager = $entityManager;
        $this->setCalendar($calendarManager->getCurrentCalendar());
    }

    /**
     * @var null|Line
     */
    private $line;

    /**
     * @param $id
     * @return Line
     */
    public function find($id): Line
    {
        $this->line = $this->getEntityManager()->getRepository(Line::class)->find($id);
        if (! $this->line instanceof Line)
            $this->line = new Line();

        $this->line->setCalendar($this->calendar);
        return $this->line;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
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
     * @return LineManager
     */
    public function setCalendar(Calendar $calendar): LineManager
    {
        $this->calendar = $calendar;
        return $this;
    }
}