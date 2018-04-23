<?php
namespace App\Timetable\Util;


class TimetableDisplayManager extends TimetableManager
{
    /**
     * @var string
     */
    private $title = 'timetable.display.title';

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return TimetableDisplayManager
     */
    public function setTitle(string $title): TimetableDisplayManager
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return false|string
     */
    public function getTimeTableDisplayDate()
    {
        return date('Ymd');
    }

    /**
     * Generate TimeTable
     *
     * @param $identifier
     * @param $displayDate
     */
    public function generateTimeTable($identifier, $displayDate)
    {
        if (false === $this->isValidTimeTable() || empty($identifier))
            return;

        if (!$this->parseIdentifier($identifier))
            return;

        $this->getSession()->set('tt_displayDate', $displayDate);

        $this->setDisplayDate(new \DateTime($displayDate))
            ->generateWeeks();

        $dayDate = $this->getDisplayDate()->format('Ymd');
        foreach ($this->getWeeks() as $q => $week) {
            if ($week->start->format('Ymd') <= $dayDate && $week->finish->format('Ymd') >= $dayDate) {
                $this->setWeek($week);
                break;
            }
        }
        $this->mapCalendarWeek();

        $actSearch = 'generate' . ucfirst($this->gettype()) . 'Activities';
        foreach ($this->getWeek()->days as $q => $day) {
            $day->class = '';
            foreach ($day->ttday->getPeriods() as $p => $period)
                $period->activity = $this->$actSearch($period);
            if (isset($day->specialDay))
                $day = $this->manageSpecialDay($day);
        }

        $this->today = new \DateTime('today');
    }

    /**
     * @var string
     */
    private $header = '';

    /**
     * @return string
     */
    public function getHeader(): string
    {
        return $this->header = empty($this->header) ? 'timetable.header.blank' : $this->header;
    }

    /**
     * @param string $header
     * @return TimetableDisplayManager
     */
    public function setHeader(string $header): TimetableDisplayManager
    {
        $this->header = $header;
        return $this;
    }

    /**
     * @var string
     */
    private $idDesc;

    /**
     * @return string
     */
    public function getIdDesc(): string
    {
        return $this->idDesc ?: '';
    }

    /**
     * @param string $idDesc
     * @return TimetableDisplayManager
     */
    public function setIdDesc(string $idDesc): TimetableDisplayManager
    {
        $this->idDesc = $idDesc;
        return $this;
    }

    /**
     * @var \DateTime
     */
    private $displayDate;

    /**
     * @return \DateTime
     */
    public function getDisplayDate(): \DateTime
    {
        $this->displayDate = $this->displayDate instanceof \DateTime ? $this->displayDate : new \DateTime();
        return $this->displayDate;
    }

    /**
     * @param \DateTime $displayDate
     * @return TimetableDisplayManager
     */
    public function setDisplayDate(\DateTime $displayDate): TimeTableDisplayManager
    {
        if ($displayDate < $this->getCalendar()->getFirstDay())
            $displayDate = $this->getCalendar()->getFirstDay();

        if ($displayDate > $this->getCalendar()->getLastDay())
            $displayDate = $this->getCalendar()->getLastDay();

        $this->displayDate = $displayDate;

        return $this;
    }

    /**
     * @var \stdClass
     */
    private $week;

    /**
     * @return \stdClass
     */
    public function getWeek(): \stdClass
    {
        $this->week = $this->week instanceof \stdClass ? $this->week : new \stdClass();
        return $this->week;
    }

    /**
     * @param \stdClass $week
     * @return TimetableDisplayManager
     */
    public function setWeek(\stdClass $week): TimetableDisplayManager
    {
        $this->week = $week;
        return $this;
    }
}