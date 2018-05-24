<?php
namespace App\Entity;

use App\Timetable\Entity\TimetableDayExtension;

class TimetableDay extends TimetableDayExtension
{
    /**
     * @var null|integer
     */
    private $id;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return TimetableDay
     */
    public function setId(?int $id): TimetableDay
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @var null|string
     */
    private $name;

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param null|string $name
     * @return TimetableDay
     */
    public function setName(?string $name): TimetableDay
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @var boolean
     */
    private $dayType;

    /**
     * @return bool
     */
    public function isDayType(): bool
    {
        return $this->dayType ? true : false ;
    }

    /**
     * @param null|bool $dayType
     * @return TimetableDay
     */
    public function setDayType(?bool $dayType): TimetableDay
    {
        $this->dayType = $dayType ? true : false;
        return $this;
    }

    /**
     * @var null|Timetable
     */
    private $timetable;

    /**
     * @return Timetable|null
     */
    public function getTimetable(): ?Timetable
    {
        return $this->timetable;
    }

    /**
     * @param Timetable|null $timetable
     * @return TimetableDay
     */
    public function setTimetable(?Timetable $timetable, $add = true): TimetableDay
    {
        if (empty($timetable)) {
            $this->timetable = null;
            return $this;
        }

        if ($add)
            $timetable->addDay($this, false);

        $this->timetable = $timetable;

        return $this;
    }
}