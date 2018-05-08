<?php
namespace App\Entity;

use App\Timetable\Entity\TimetableColumnExtension;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;

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
        return strtoupper($this->code);
    }

    /**
     * @param null|string $code
     * @return TimetableColumn
     */
    public function setCode(?string $code): TimetableColumn
    {
        $this->code = strtoupper($code);
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
     * @return Collection
     */
    public function getPeriods(): Collection
    {
        if (empty($this->periods))
            $this->periods = new ArrayCollection();

        if ($this->periods instanceof PersistentCollection && ! $this->periods->isInitialized())
            $this->periods->initialize();

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
     * @param TimetablePeriod|null $period
     * @param bool $add
     * @return TimetableColumn
     */
    public function addPeriod(?TimetablePeriod $period, $add = true): TimetableColumn
    {
        if (empty($period) || $this->getPeriods()->contains($period))
            return $this;

        if ($add)
            $period->setColumn($this, false);

        $this->periods->add($period);

        return $this;
    }

    /**
     * @param TimetablePeriod|null $period
     * @return TimetableColumn
     */
    public function removePeriod(?TimetablePeriod $period): TimetableColumn
    {
        $this->getPeriods()->removeElement($period);

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
    public function setTimetable(?Timetable $timetable, $add = true): TimetableColumn
    {
        if ($add)
            $timetable->addColumn($this, false);

        $this->timetable = $timetable;

        return $this;
    }
}