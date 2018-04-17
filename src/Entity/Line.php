<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;

class Line
{
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
     * @return Line
     */
    public function setId(?int $id): Line
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
     * @return Line
     */
    public function setName(?string $name): Line
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
     * @return Line
     */
    public function setCode(?string $code): Line
    {
        $this->code = $code;
        return $this;
    }
    /**
     * @var null|integer
     */
    private $participants;

    /**
     * @return int|null
     */
    public function getParticipants(): ?int
    {
        return $this->participants;
    }

    /**
     * @param int|null $participants
     * @return Line
     */
    public function setParticipants(?int $participants): Line
    {
        $this->participants = $participants;
        return $this;
    }

    /**
     * @var boolean
     */
    private $includeAll;

    /**
     * @return bool
     */
    public function isIncludeAll(): bool
    {
        return $this->includeAll ? true : false ;
    }

    /**
     * @param bool $includeAll
     * @return Line
     */
    public function setIncludeAll(?bool $includeAll): Line
    {
        $this->includeAll = $includeAll ? true : false ;
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
     * @return Line
     */
    public function setCalendar(Calendar $calendar): Line
    {
        $this->calendar = $calendar;
        return $this;
    }

    /**
     * @var null|Course
     */
    private $course;

    /**
     * @return Course|null
     */
    public function getCourse(): ?Course
    {
        return $this->course;
    }

    /**
     * @param Course|null $course
     * @return Line
     */
    public function setCourse(?Course $course): Line
    {
        $this->course = $course;
        return $this;
    }

    /**
     * @var null|Collection
     */
    private $activities;

    /**
     * @return Collection
     */
    public function getActivities(): Collection
    {
        if (empty($this->activities))
            $this->activities = new ArrayCollection();

        if ($this->activities instanceof PersistentCollection && ! $this->activities->isInitialized())
            $this->activities->initialize();

        return $this->activities;
    }

    /**
     * @param Collection|null $activities
     * @return Line
     */
    public function setActivities(?Collection $activities): Line
    {
        $this->activities = $activities;
        return $this;
    }

    /**
     * @param Activity|null $activity
     * @param bool $add
     * @return Line
     */
    public function addActivity(?Activity $activity): Line
    {
        if (empty($activity) || $this->getActivities()->contains($activity))
            return $this;

        $this->getActivities()->add($activity);

        return $this;
    }

    /**
     * @param Activity|null $activity
     * @return Line
     */
    public function removeActivity(?Activity $activity): Line
    {
        $this->getActivities()->removeElement($activity);
        return $this;
    }
}