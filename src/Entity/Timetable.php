<?php
namespace App\Entity;

use App\Timetable\Extension\TimetableExtension;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;

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

        if ($this->days instanceof PersistentCollection && ! $this->days->isInitialized())
            $this->days->initialize();

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
     * @param TimetableDay|null $day
     * @param bool $add
     * @return Timetable
     */
    public function addDay(?TimetableDay $day, $add = true): Timetable
    {
        if (empty($day) || $this->getDays()->contains($day))
            return $this;

        if ($add)
            $day->setTimetable($this, false);

        $this->days->add($day);

        return $this;
    }

    /**
     * @param TimetableDay|null $day
     * @return Timetable
     */
    public function removeDay(?TimetableDay $day): Timetable
    {
        $this->getDays()->removeElement($day);

        $day->setTimetable(null, false);

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

        if ($this->columns instanceof PersistentCollection && ! $this->columns->isInitialized())
            $this->columns->initialize();
        
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
     * @param TimetableColumn|null $column
     * @param bool $add
     * @return Timetable
     */
    public function addColumn(?TimetableColumn $column, $add = true): Timetable
    {
        if (empty($column) || $this->getColumns()->contains($column))
            return $this;

        if ($add)
            $column->setTimetable($this, false);

        $this->columns->add($column);

        return $this;
    }

    /**
     * @param TimetableDay|null $day
     * @return Timetable
     */
    public function removeColumn(?TimetableColumn $column): Timetable
    {
        $this->getColumns()->removeElement($column);

        $column->setTimetable(null, false);

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