<?php
namespace App\Timetable\Util;


use App\Entity\Calendar;

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
    public function getTimetableDisplayDate()
    {
        return date('Ymd');
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