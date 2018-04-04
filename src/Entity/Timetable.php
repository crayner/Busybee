<?php
namespace App\Entity;

use App\Timetable\Extension\TimetableExtension;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Timetable extends TimetableExtension
{
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
     * @return Timetable
     */
    public function setName(?string $name): Timetable
    {
        $this->name = $name;
        return $this;
    }

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
     * @return Timetable
     */
    public function setId(?int $id): Timetable
    {
        return $this;
    }

    /**
     * @var null|string
     */
    private $code;

    /**
     * @return null|string
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param null|string $code
     * @return Timetable
     */
    public function setCode(?string $code): Timetable
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @var null|Calendar
     */
    private $calendar;

    /**
     * @return Calendar|null
     */
    public function getCalendar(): ?Calendar
    {
        return $this->calendar;
    }

    /**
     * @param Calendar|null $calendar
     * @return Timetable
     */
    public function setCalendar(?Calendar $calendar): Timetable
    {
        $this->calendar = $calendar;
        return $this;
    }

    /**
     * @var null|Collection
     */
    private $days;

    /**
     * @return Collection
     */
    public function getDays(): Collection
    {
        if (empty($this->days))
            $this->days = new ArrayCollection();

        return $this->days;
    }

    /**
     * @param Collection|null $days
     * @return Timetable
     */
    public function setDays(?Collection $days): Timetable
    {
        $this->days = $days;
        return $this;
    }

    /**
     * @var null|Collection
     */
    private $columns;

    /**
     * @return Collection
     */
    public function getColumns(): Collection
    {
        if (empty($this->columns))
            $this->columns = new ArrayCollection();

        return $this->columns;
    }

    /**
     * @param Collection|null $columns
     * @return Timetable
     */
    public function setColumns(?Collection $columns): Timetable
    {
        $this->columns = $columns;
        return $this;
    }

    /**
     * @var boolean
     */
    private $locked = false;

    /**
     * @return bool
     */
    public function isLocked(): bool
    {
        return $this->locked ? true : false ;
    }

    /**
     * @param null|bool $locked
     * @return Timetable
     */
    public function setLocked(?bool $locked): Timetable
    {
        $this->locked = $locked ? true : false ;
        return $this;
    }

    /**
     * @var boolean
     */
    private $generated = false;

    /**
     * @return bool
     */
    public function isGenerated(): bool
    {
        return $this->generated ? true : false ;
    }

    /**
     * @param null|bool $generated
     * @return Timetable
     */
    public function setGenerated(?bool $generated): Timetable
    {
        $this->generated = $generated ? true : false ;
        return $this;
    }
}