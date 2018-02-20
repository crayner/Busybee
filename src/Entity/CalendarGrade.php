<?php
namespace App\Entity;


use App\Calendar\Entity\CalendarGradeExtension;
use Doctrine\Common\Collections\Collection;
use Hillrange\Security\Util\UserTrackInterface;
use Hillrange\Security\Util\UserTrackTrait;

class CalendarGrade extends CalendarGradeExtension implements UserTrackInterface
{
    use UserTrackTrait;

    /**
     * @var null|integer
     */
    private $id;

    /**
     * @var null|string
     */
    private $grade;

    /**
     * @var null|Calendar
     */
    private $calendar;

    /**
     * @var null|Collection
     */
    private $students;

    /**
     * @var CalendarGrade
     */
    private $nextGrade;

    /**
     * @var null|integer
     */
    private $sequence;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getGrade(): ?string
    {
        return $this->grade;
    }

    /**
     * @param null|string $grade
     * @return CalendarGrade
     */
    public function setGrade(?string $grade): CalendarGrade
    {
        $this->grade = $grade;

        return $this;
    }

    /**
     * @return Calendar|null
     */
    public function getCalendar(): ?Calendar
    {
        return $this->calendar;
    }

    /**
     * @param Calendar|null $calendar
     * @return CalendarGrade
     */
    public function setCalendar(?Calendar $calendar): CalendarGrade
    {
        $this->calendar = $calendar;

        return $this;
    }

    /**
     * @return Collection|null
     */
    public function getStudents(): ?Collection
    {
        return $this->students;
    }

    /**
     * @param Collection|null $students
     * @return CalendarGrade
     */
    public function setStudents(?Collection $students): CalendarGrade
    {
        $this->students = $students;
        return $this;
    }

    /**
     * @param Student|null $student
     * @param bool $add
     * @return CalendarGrade
     */
    public function addStudent(?Student $student, $add = true): CalendarGrade
    {
        if (empty($student))
            return $this;

        if ($add)
            $student->addCalendarGrade($this, false);

        if (!$this->students->contains($student))
            $this->students->add($student);

        return $this;
    }

    /**
     * @param Student|null $student
     * @param bool $remove
     * @return CalendarGrade
     */
    public function removeStudent(?Student $student, $remove = true): CalendarGrade
    {
        if (empty($student))
            return $this;

        if ($this->students->contains($student)) {
            if ($remove)
                $student->removeCalendarGrade($this, false);
            $this->students->removeElement($student);
        }

        return $this;
    }

    /**
     * @return CalendarGrade
     */
    public function getNextGrade(): ?CalendarGrade
    {
        return $this->nextGrade;
    }

    /**
     * @param CalendarGrade $nextGrade
     * @return CalendarGrade
     */
    public function setNextGrade(?CalendarGrade $nextGrade): CalendarGrade
    {
        $this->nextGrade = $nextGrade;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getSequence(): ?int
    {
        return $this->sequence;
    }

    /**
     * @param int|null $sequence
     * @return CalendarGrade
     */
    public function setSequence(?int $sequence): CalendarGrade
    {
        $this->sequence = $sequence;
        return $this;
    }

    private $activities;
}