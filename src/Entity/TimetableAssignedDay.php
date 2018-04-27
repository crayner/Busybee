<?php
namespace App\Entity;

class TimetableAssignedDay
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
     * @return TimetableAssignedDay
     */
    public function setId(?int $id): TimetableAssignedDay
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @var null|\DateTime
     */
    private $day;

    /**
     * @return \DateTime|null
     */
    public function getDay(): ?\DateTime
    {
        return $this->day;
    }

    /**
     * @param \DateTime|null $day
     * @return TimetableAssignedDay
     */
    public function setDay(?\DateTime $day): TimetableAssignedDay
    {
        $this->day = $day;
        return $this;
    }

    /**
     * @var boolean
     */
    private $startRotate = false;

    /**
     * @return bool
     */
    public function isStartRotate(): bool
    {
        $this->startRotate = $this->startRotate ? true : false ;
        return $this->startRotate ;
    }

    /**
     * @param bool $startRotate
     * @return TimetableAssignedDay
     */
    public function setStartRotate(bool $startRotate): TimetableAssignedDay
    {
        $this->startRotate = $startRotate ? true : false;
        return $this;
    }

    /**
     * @var null|integer
     */
    private $week;

    /**
     * @return null|int
     */
    public function getWeek(): ?int
    {
        return $this->week;
    }

    /**
     * @param integer|null $week
     * @return TimetableAssignedDay
     */
    public function setWeek(?int $week): TimetableAssignedDay
    {
        $this->week = $week;
        return $this;
    }

    /**
     * @var null|string
     */
    private $type;

    /**
     * @return null|string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param null|string $type
     * @return TimetableAssignedDay
     */
    public function setType(?string $type): TimetableAssignedDay
    {
        if (! in_array($type, $this->getTypeList()))
            throw new \InvalidArgumentException(sprintf('The type %s is not defined as a Timetable Day Type.', $type));

        $this->type = $type;
        return $this;
    }

    /**
     * @return array
     */
    public function getTypeList(): array
    {
        return ['school_day', 'holiday', 'closure', 'no_school', 'alter'];
    }

    /**
     * @var Timetable|null
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
     * @return TimetableAssignedDay
     */
    public function setTimetable(?Timetable $timetable): TimetableAssignedDay
    {
        $this->timetable = $timetable;
        return $this;
    }

    /**
     * @var Term|null
     */
    private $term;

    /**
     * @return Term|null
     */
    public function getTerm(): ?Term
    {
        return $this->term;
    }

    /**
     * @param Term|null $term
     * @return TimetableAssignedDay
     */
    public function setTerm(?Term $term): TimetableAssignedDay
    {
        $this->term = $term;
        return $this;
    }

    /**
     * @var null|TimetableColumn
     */
    private $column;

    /**
     * @return TimetableColumn|null
     */
    public function getColumn(): ?TimetableColumn
    {
        return $this->column;
    }

    /**
     * @param TimetableColumn|null $column
     * @return TimetableAssignedDay
     */
    public function setColumn(?TimetableColumn $column): TimetableAssignedDay
    {
        $this->column = $column;
        return $this;
    }

    /**
     * @var null|SpecialDay
     */
    private $specialDay;

    /**
     * @return SpecialDay|null
     */
    public function getSpecialDay(): ?SpecialDay
    {
        return $this->specialDay;
    }

    /**
     * @param SpecialDay|null $specialDay
     * @return TimetableAssignedDay
     */
    public function setSpecialDay(?SpecialDay $specialDay): TimetableAssignedDay
    {
        $this->specialDay = $specialDay;
        return $this;
    }
}