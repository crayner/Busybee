<?php
namespace App\Entity;

use App\People\Entity\StudentCalendarExtension;

class StudentCalendar extends StudentCalendarExtension
{
    /**
     * @var int|null
     */
    private $id;

    /**
     * @var string|null
     */
    private $status;

    /**
     * @var Student|null
     */
    private $student;

    /**
     * @var CalendarGroup|null
     */
    private $calendarGroup;

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
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param null|string $status
     * @return StudentCalendar
     */
    public function setStatus(?string $status): StudentCalendar
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Student|null
     */
    public function getStudent(): ?Student
    {
        return $this->student;
    }

    /**
     * @param Student|null $student
     * @return StudentCalendar
     */
    public function setStudent(?Student $student): StudentCalendar
    {
        $this->student = $student;
        return $this;
    }

    /**
     * @return CalendarGroup|null
     */
    public function getCalendarGroup(): ?CalendarGroup
    {
        return $this->calendarGroup;
    }

    /**
     * @param CalendarGroup|null $calendarGroup
     * @return StudentCalendar
     */
    public function setCalendarGroup(?CalendarGroup $calendarGroup): StudentCalendar
    {
        $this->calendarGroup = $calendarGroup;
        return $this;
    }
}