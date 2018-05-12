<?php
namespace App\Entity;

use App\Timetable\Entity\TimetablePeriodExtension;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;

class TimetablePeriod extends TimetablePeriodExtension
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
     * @return TimetablePeriod
     */
    public function setId(?int $id): TimetablePeriod
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
     * @return TimetablePeriod
     */
    public function setName(?string $name): TimetablePeriod
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
     * @return TimetablePeriod
     */
    public function setCode(?string $code): TimetablePeriod
    {
        $this->code = strtoupper($code);
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
     * @return TimetablePeriod
     */
    public function setStart(?\DateTime $start): TimetablePeriod
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
     * @return TimetablePeriod
     */
    public function setEnd(?\DateTime $end): TimetablePeriod
    {
        $this->end = $end;
        return $this;
    }

    /**
     * @var null|Collection
     */
    private $activities;

    /**
     * @return Collection|null
     */
    public function getActivities(): ?Collection
    {
        if (empty($this->activities))
            $this->activities = new ArrayCollection();

        if ($this->activities instanceof PersistentCollection && ! $this->activities->isInitialized())
            $this->activities->initialize();



        return $this->activities;
    }

    /**
     * @param Collection|null $activities
     * @return TimetablePeriod
     */
    public function setActivities(?Collection $activities): TimetablePeriod
    {
        $this->activities = $activities;
        return $this;
    }

    /**
     * @param TimetablePeriodActivity|null $activity
     * @param bool $add
     * @return TimetablePeriod
     */
    public function addActivity(?TimetablePeriodActivity $activity, $add = true): TimetablePeriod
    {
        if (empty($activity) || $this->getActivities()->contains($activity))
            return $this;

        if ($add)
            $activity->setPeriod($this, false);

        $this->activities->add($activity);

        return $this;
    }

    /**
     * @param TimetablePeriodActivity|null $activity
     * @return TimetablePeriod
     */
    public function removeActivity(?TimetablePeriodActivity $activity): TimetablePeriod
    {
        if ($activity) {
            $activity->setPeriod(null, false);
            $this->getActivities()->removeElement($activity);
        }

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
     * @return TimetablePeriod
     */
    public function setColumn(?TimetableColumn $column, $add = true): TimetablePeriod
    {
        if ($add)
            $column->addPeriod($this, false);

        $this->column = $column;

        return $this;
    }

    /**
     * @var string
     */
    private $periodType;

    /**
     * @return string
     */
    public function getPeriodType(): string
    {
        if (! in_array($this->periodType, self::getPeriodTypeList('flat')))
            $this->periodType = 'class';
        return $this->periodType;
    }

    /**
     * @param string $periodType
     * @return TimetablePeriod
     */
    public function setPeriodType(?string $periodType): TimetablePeriod
    {
        if (! in_array($this->periodType, self::getPeriodTypeList('flat')))
            $periodType = 'class';
        $this->periodType = $periodType;
        return $this;
    }
}