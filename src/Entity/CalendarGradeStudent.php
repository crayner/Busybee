<?php
namespace App\Entity;


use Hillrange\Security\Util\UserTrackInterface;
use Hillrange\Security\Util\UserTrackTrait;

class CalendarGradeStudent implements UserTrackInterface
{
    use UserTrackTrait;

    /**
     * @var integer|null
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
     * @return CalendarGradeStudent
     */
    public function setId(?int $id): CalendarGradeStudent
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @var null|string
     */
    private $status;

    /**
     * @return null|string
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param null|string $status
     * @return CalendarGradeStudent
     */
    public function setStatus(?string $status): CalendarGradeStudent
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @var Student|null
     */
    private $student;

    /**
     * @return Student|null
     */
    public function getStudent(): ?Student
    {
        return $this->student;
    }

    /**
     * @param Student|null $student
     * @return CalendarGradeStudent
     */
    public function setStudent(?Student $student, $add = true): CalendarGradeStudent
    {
        if ($student && $add)
            $student->addCalendarGrade($this, false);

        $this->student = $student;
        return $this;
    }

    /**
     * @var CalendarGrade|null
     */
    private $calendarGrade;

    /**
     * @return CalendarGrade|null
     */
    public function getCalendarGrade(): ?CalendarGrade
    {
        return $this->calendarGrade;
    }

    /**
     * @param CalendarGrade|null $calendarGrade
     * @return CalendarGradeStudent
     */
    public function setCalendarGrade(?CalendarGrade $calendarGrade, $add = true): CalendarGradeStudent
    {
        if ($calendarGrade && $add)
            $calendarGrade->addStudent($this, false);

        $this->calendarGrade = $calendarGrade;
        return $this;
    }

    /**
     * @return string
     */
    public function getFullGradeName(): string
    {
        return $this->getCalendarGrade()->getFullName();
    }

    /**
     * @param array $options
     * @return string
     */
    public function getFullStudentName(array $options = []): string
    {
        return $this->getStudent()->getFullName($options);
    }
}