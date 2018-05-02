<?php
namespace App\Entity;

use App\Timetable\Entity\TimetablePeriodActivityTutorExtension;
use Hillrange\Security\Util\UserTrackInterface;
use Hillrange\Security\Util\UserTrackTrait;

class TimetablePeriodActivityTutor extends TimetablePeriodActivityTutorExtension implements UserTrackInterface
{
    use UserTrackTrait;
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
     * @param int|null $id
     * @return TimetablePeriodActivityTutor
     */
    public function setId(?int $id): TimetablePeriodActivityTutor
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @var null|string
     */
    private $role;

    /**
     * @return null|string
     */
    public function getRole(): ?string
    {
        return $this->role;
    }

    /**
     * @param null|string $role
     * @return TimetablePeriodActivityTutor
     */
    public function setRole(?string $role): TimetablePeriodActivityTutor
    {
        $this->role = $role;
        return $this;
    }

    /**
     * @var null|Staff
     */
    private $tutor;

    /**
     * @return Staff|null
     */
    public function getTutor(): ?Staff
    {
        return $this->tutor;
    }

    /**
     * @param Staff|null $tutor
     * @return TimetablePeriodActivityTutor
     */
    public function setTutor(?Staff $tutor, $add = true): TimetablePeriodActivityTutor
    {
        if (empty($tutor))
            return $this;

        if ($add)
            $tutor->addPeriodCommitment($this, false);

        $this->tutor = $tutor;

        return $this;
    }

    /**
     * @var null|TimetablePeriodActivity
     */
    private $activity;

    /**
     * @return Activity|null
     */
    public function getActivity(): ?TimetablePeriodActivity
    {
        return $this->activity;
    }

    /**
     * @param TimetablePeriodActivity|null $activity
     * @return TimetablePeriodActivityTutor
     */
    public function setActivity(?TimetablePeriodActivity $activity, $add = true): TimetablePeriodActivityTutor
    {
        if ($add && $activity)
            $activity->addTutor($this, false);

        $this->activity = $activity;

        return $this;
    }

    /**
     * @var null|integer
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
     * @return TimetablePeriodActivityTutor
     */
    public function setSequence(?int $sequence): TimetablePeriodActivityTutor
    {
        $this->sequence = $sequence;
        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        if ($this->getTutor() && $this->getActivity())
            return $this->getTutor()->getFullName() . ' - ' . $this->getActivity()->getFullName();
        if ($this->getActivity())
            return $this->getActivity()->getFullName();
        return $this->getId(). ' has an empty tutors and activity.';
    }
}