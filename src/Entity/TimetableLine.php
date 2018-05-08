<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;

class TimetableLine
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
     * @var null|Collection
     */
    private $courses;

    /**
     * @return Collection|null
     */
    public function getCourses(): Collection
    {
        if (empty($this->courses))
            $this->courses = new ArrayCollection();

        if ($this->courses instanceof PersistentCollection && ! $this->courses->isInitialized())
            $this->courses->initialize();

        return $this->courses;
    }

    /**
     * @param Collection|null $courses
     * @return TimetableLine
     */
    public function setCourses(?Collection $courses): TimetableLine
    {
        $this->courses = $courses;
        return $this;
    }

    /**
     * @param Course|null $course
     * @param bool $add
     * @return TimetableLine
     */
    public function addCourse(?Course $course, $add = true): TimetableLine
    {
        if (empty($course) || $this->getCourses()->contains($course))
            return $this;

        if ($add)
            $course->setLine($this, false);

        $this->getCourses()->add($course);

        return $this;
    }

    /**
     * @param Course|null $course
     * @return TimetableLine
     */
    public function removeCourse(?Course $course): TimetableLine
    {
        $this->getCourses()->removeElement($course);

        if ($course instanceof Course)
            $course->setLine(null);

        return $this;
    }
}