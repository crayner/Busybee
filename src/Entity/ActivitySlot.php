<?php
namespace App\Entity;

use Hillrange\Security\Util\UserTrackInterface;
use Hillrange\Security\Util\UserTrackTrait;

class ActivitySlot implements UserTrackInterface
{
    use UserTrackTrait;

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
     * @var string|null
     */
    private $externalLocation;

    /**
     * @return null|string
     */
    public function getExternalLocation(): ?string
    {
        return $this->externalLocation;
    }

    /**
     * @param null|string $externalLocation
     * @return ActivitySlot
     */
    public function setExternalLocation(?string $externalLocation): ActivitySlot
    {
        $this->externalLocation = $externalLocation;
        return $this;
    }

    /**
     * @var null|\DateTime
     */
    private $startTime;

    /**
     * @return \DateTime|null
     */
    public function getStartTime(): ?\DateTime
    {
        return $this->startTime;
    }

    /**
     * @param \DateTime|null $startTime
     * @return ActivitySlot
     */
    public function setStartTime(?\DateTime $startTime): ActivitySlot
    {
        $this->startTime = $startTime;
        return $this;
    }

    /**
     * @var null|\DateTime
     */
    private $endTime;

    /**
     * @return \DateTime|null
     */
    public function getEndTime(): ?\DateTime
    {
        return $this->endTime;
    }

    /**
     * @param \DateTime|null $endTime
     * @return ActivitySlot
     */
    public function setEndTime(?\DateTime $endTime): ActivitySlot
    {
        $this->endTime = $endTime;
        return $this;
    }

    /**
     * @var null|ExternalActivity
     */
    private $activity;

    /**
     * @return ExternalActivity|null
     */
    public function getActivity(): ?ExternalActivity
    {
        return $this->activity;
    }

    /**
     * @param ExternalActivity $activity
     * @return ActivitySlot
     */
    public function setActivity(ExternalActivity $activity, $add = true): ActivitySlot
    {
        if ($add)
            $activity->addActivitySlot($this, false);

        $this->activity = $activity;

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
     * @return ActivitySlot
     */
    public function setSpace(?Space $space): ActivitySlot
    {
        $this->space = $space;
        return $this;
    }

    /**
     * @var null|string
     */
    private $day;

    /**
     * @return null|string
     */
    public function getDay(): ?string
    {
        return $this->day;
    }

    /**
     * @param null|string $day
     * @return ActivitySlot
     */
    public function setDay(?string $day): ActivitySlot
    {
        $this->day = $day;
        return $this;
    }

    /**
     * @var string
     */
    private $type;

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type == '1' ? '1' : '0';
    }

    /**
     * @param string $type
     * @return ActivitySlot
     */
    public function setType(string $type): ActivitySlot
    {
        $this->type = $type == '1' ? '1' : '0';
        return $this;
    }
}