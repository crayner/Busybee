<?php
namespace App\Entity;

use App\Timetable\Extension\TimetableColumnExtension;
use Doctrine\Common\Collections\Collection;

class TimetableColumn extends TimetableColumnExtension
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
     * @return TimetableColumn
     */
    public function setId(?int $id): TimetableColumn
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
     * @return TimetableColumn
     */
    public function setName(?string $name): TimetableColumn
    {
        $this->name = $name;
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
     * @return TimetableColumn
     */
    public function setCode(?string $code): TimetableColumn
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @var null|string
     */
    private $mappingInfo;

    /**
     * @return null|string
     */
    public function getMappingInfo(): ?string
    {
        return $this->mappingInfo;
    }

    /**
     * @param null|string $mappingInfo
     * @return TimetableColumn
     */
    public function setMappingInfo(?string $mappingInfo): TimetableColumn
    {
        $this->mappingInfo = $mappingInfo;
        return $this;
    }

    /**
     * @var null|\DateTime
     */
    private $start;

    /**
     * @return \DateTime|null
     */
    public function getStart(): ?\DateTime
    {
        return $this->start;
    }

    /**
     * @param \DateTime|null $start
     * @return TimetableColumn
     */
    public function setStart(?\DateTime $start): TimetableColumn
    {
        $this->start = $start;
        return $this;
    }

    /**
     * @var null|\DateTime
     */
    private $end;

    /**
     * @return \DateTime|null
     */
    public function getEnd(): ?\DateTime
    {
        return $this->end;
    }

    /**
     * @param \DateTime|null $end
     * @return TimetableColumn
     */
    public function setEnd(?\DateTime $end): TimetableColumn
    {
        $this->end = $end;
        return $this;
    }

    /**
     * @var null|integer
     */
    private $sequence;

    /**
     * @return int|null
     */
    public function getSequence(): ?int
    {
        return $this->sequence;
    }

    /**
     * @param int|null $sequence
     * @return TimetableColumn
     */
    public function setSequence(?int $sequence): TimetableColumn
    {
        $this->sequence = $sequence;
        return $this;
    }

    /**
     * @var null|Collection
     */
    private $periods;

    /**
     * @return Collection|null
     */
    public function getPeriods(): ?Collection
    {
        return $this->periods;
    }

    /**
     * @param Collection|null $periods
     * @return TimetableColumn
     */
    public function setPeriods(?Collection $periods): TimetableColumn
    {
        $this->periods = $periods;
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
     * @return TimetableColumn
     */
    public function setTimetable(?Timetable $timetable): TimetableColumn
    {
        $this->timetable = $timetable;
        return $this;
    }
}