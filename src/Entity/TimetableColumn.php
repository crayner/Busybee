<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;
use Hillrange\Security\Util\UserTrackInterface;
use Hillrange\Security\Util\UserTrackTrait;

class TimetableColumn implements UserTrackInterface
{
    use UserTrackTrait;

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
     * @var integer|null
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
     * @var null|Timetable
     */
    private $timetable;

    /**
     * @return TimetableColumn|null
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

    /**
     * @var Collection|null
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
     * @return TimetableColumn
     */
    public function setDays(?Collection $days): TimetableColumn
    {
        $this->days = $days;
        return $this;
    }

    /**
     * @param TimetableDay|null $day
     * @return TimetableColumn
     */
    public function addDay(?TimetableDay $day): TimetableColumn
    {
        if (empty($day) || $this->getDays()->contains($day))
            return $this;

        $day->setColumn($this);

        $this->days->add($day);

        return $this;
    }

    /**
     * @param TimetableDay|null $day
     * @return TimetableColumn
     */
    public function removeDay(?TimetableDay $day): TimetableColumn
    {
        $this->getDays()->removeElement($day);

        return $this;
    }

    /**
     * @var Collection|null
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
     * @param TimetableColumnPeriod|null $period
     * @return TimetableColumn
     */
    public function addPeriod(?TimetableColumnPeriod $period): TimetableColumn
    {
        if (empty($period) || $this->getPeriods()->contains($period))
            return $this;

        $period->setColumn($this);

        $this->periods->add($period);

        return $this;
    }

    /**
     * @param TimetableColumnPeriod|null $period
     * @return TimetableColumn
     */
    public function removePeriod(?TimetableColumnPeriod $period): TimetableColumn
    {
        $this->getPeriods()->removeElement($period);

        return $this;
    }
}