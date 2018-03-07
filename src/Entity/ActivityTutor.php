<?php
namespace App\Entity;


class ActivityTutor
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
     * @return ActivityTutor
     */
    public function setRole(?string $role): ActivityTutor
    {
        $this->role = $role;
        return $this;
    }

    /**
     * @var null|Person
     */
    private $tutor;

    /**
     * @return Person|null
     */
    public function getTutor(): ?Person
    {
        return $this->tutor;
    }

    /**
     * @param Person|null $tutor
     * @return ActivityTutor
     */
    public function setTutor(?Person $tutor, $add = true): ActivityTutor
    {
        if (empty($tutor))
            return $this;

        if ($add)
            $tutor->addCommitment($this, false);

        $this->tutor = $tutor;

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
     * @return ActivityTutor
     */
    public function setActivity(?Activity $activity, $add = true): ActivityTutor
    {
        if (empty($activity))
            return $this;

        if ($add)
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
     * @return ActivityTutor
     */
    public function setSequence(?int $sequence): ActivityTutor
    {
        $this->sequence = $sequence;
        return $this;
    }

    /**
     * @param int|null $id
     * @return ActivityTutor
     */
    public function setId(?int $id): ActivityTutor
    {
        $this->id = $id;
        return $this;
    }
}