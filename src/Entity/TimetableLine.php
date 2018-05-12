<?php
namespace App\Entity;

use App\Timetable\Entity\TimetableLineExtn;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;
use Hillrange\Security\Util\UserTrackInterface;
use Hillrange\Security\Util\UserTrackTrait;

class TimetableLine extends TimetableLineExtn implements UserTrackInterface
{
    use UserTrackTrait;

    /**
     * @var null|integer
     */
    private $id;

    /**
     * @return null|integer
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param null|integer $id
     * @return TimetableLine
     */
    public function setId(?int $id): TimetableLine
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
     * @return TimetableLine
     */
    public function setName(?string $name): TimetableLine
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
     * @return TimetableLine
     */
    public function setCode(?string $code): TimetableLine
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @var Calendar
     */
    private $calendar;

    /**
     * @return null|Calendar
     */
    public function getCalendar(): ?Calendar
    {
        return $this->calendar;
    }

    /**
     * @param Calendar $calendar
     * @return TimetableLine
     */
    public function setCalendar(Calendar $calendar): TimetableLine
    {
        $this->calendar = $calendar;
        return $this;
    }

    /**
     * @var Collection|null
     */
    private $activities;

    /**
     * @return Collection|null
     */
    public function getActivities(): ?Collection
    {
        if (empty($this->activities))
            $this->activities = new ArrayCollection();

        if ($this->activities instanceof PersistentCollection)
            $this->activities->initialize();

        return $this->activities;
    }

    /**
     * @param Collection|null $activities
     * @return TimetableLine
     */
    public function setActivities(?Collection $activities): TimetableLine
    {
        $this->activities = $activities;
        return $this;
    }


}