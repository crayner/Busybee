<?php
namespace App\Entity;

use App\Timetable\Extension\TimetablePeriodActivityExtension;

class TimetablePeriodActivity extends TimetablePeriodActivityExtension
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
     * @return TimetablePeriodActivity
     */
    public function setId(?int $id): TimetablePeriodActivity
    {
        return $this;
    }

    /**
     * @var null|Space
     */
    private $space;

    /**
     * @return Space|null
     */
    public function getSpace(): ?Space
    {
        return $this->space;
    }

    /**
     * @param Space|null $space
     * @return TimetablePeriodActivity
     */
    public function setSpace(?Space $space): TimetablePeriodActivity
    {
        $this->space = $space;
        return $this;
    }

    /**
     * @var null|Staff
     */
    private $tutor1;

    /**
     * @return Staff|null
     */
    public function getTutor1(): ?Staff
    {
        return $this->tutor1;
    }

    /**
     * @param Staff|null $tutor1
     * @return TimetablePeriodActivity
     */
    public function setTutor1(?Staff $tutor1): TimetablePeriodActivity
    {
        $this->tutor1 = $tutor1;
        return $this;
    }

    /**
     * @var null|Staff
     */
    private $tutor2;

    /**
     * @return Staff|null
     */
    public function getTutor2(): ?Staff
    {
        return $this->tutor2;
    }

    /**
     * @param Staff|null $tutor2
     * @return TimetablePeriodActivity
     */
    public function setTutor2(?Staff $tutor2): TimetablePeriodActivity
    {
        $this->tutor2 = $tutor2;
        return $this;
    }

    /**
     * @var null|Staff
     */
    private $tutor3;

    /**
     * @return Staff|null
     */
    public function getTutor3(): ?Staff
    {
        return $this->tutor3;
    }

    /**
     * @param Staff|null $tutor3
     * @return TimetablePeriodActivity
     */
    public function setTutor3(?Staff $tutor3): TimetablePeriodActivity
    {
        $this->tutor3 = $tutor3;
        return $this;
    }

    /**
     * @var null|FaceToFace
     */
    private $activity;

    /**
     * @return FaceToFace|null
     */
    public function getActivity(): ?FaceToFace
    {
        return $this->activity;
    }

    /**
     * @param FaceToFace|null $activity
     * @return TimetablePeriodActivity
     */
    public function setActivity(?FaceToFace $activity): TimetablePeriodActivity
    {
        $this->activity = $activity;
        return $this;
    }

    /**
     * @var null|TimetablePeriod
     */
    private $period;

    /**
     * @return TimetablePeriod|null
     */
    public function getPeriod(): ?TimetablePeriod
    {
        return $this->period;
    }

    /**
     * @param TimetablePeriod|null $period
     * @return TimetablePeriodActivity
     */
    public function setPeriod(?TimetablePeriod $period): TimetablePeriodActivity
    {
        $this->period = $period;
        return $this;
    }
}