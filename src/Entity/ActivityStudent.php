<?php
namespace App\Entity;


class ActivityStudent
{
    /**
     * @var null|int
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
     * @var null|Student
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
     * @return ActivityStudent
     */
    public function setStudent(?Student $student): ActivityStudent
    {
        $this->student = $student;
        return $this;
    }

    /**
     * @var null|Activity
     */
    private $activity;

    /**
     * @return Activity|null
     */
    public function getActivity(): ?Activity
    {
        return $this->activity;
    }

    /**
     * @param Activity|null $activity
     * @return ActivityStudent
     */
    public function setActivity(?Activity $activity): ActivityStudent
    {
        $this->activity = $activity;
        return $this;
    }

}