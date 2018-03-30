<?php
namespace App\Timetable\Organism;

use App\Timetable\Util\DayDateManager;

class DayDate
{
    /**
     * @var null|integer
     */
    private $timetableId;

    /**
     * @return int|null
     */
    public function getTimetableId(): ?int
    {
        return $this->timetableId;
    }

    /**
     * @param int|null $timetableId
     * @return DayDate
     */
    public function setTimetableId(?int $timetableId): DayDate
    {
        $this->timetableId = $timetableId;
        return $this;
    }

    /**
     * DayDate constructor.
     * @param DayDateManager $dayDateManager
     */
    public function __construct(DayDateManager $dayDateManager)
    {
        $this->setTimetableId($dayDateManager->getTimetable()->getId());
    }
}