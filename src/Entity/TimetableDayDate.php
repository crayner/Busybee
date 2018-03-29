<?php
namespace App\Entity;


use Hillrange\Security\Util\UserTrackInterface;
use Hillrange\Security\Util\UserTrackTrait;

class TimetableDayDate implements UserTrackInterface
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
     * @param int|null $id
     * @return TimetableDayDate
     */
    public function setId(?int $id): TimetableDayDate
    {
        return $this;
    }

    /**
     * @var \DateTime|null
     */
    private $dayDate;

    /**
     * @return \DateTime|null
     */
    public function getDayDate(): ?\DateTime
    {
        return $this->dayDate;
    }

    /**
     * @param \DateTime|null $dayDate
     * @return TimetableDayDate
     */
    public function setDayDate(?\DateTime $dayDate): TimetableDayDate
    {
        $this->dayDate = $dayDate;
        return $this;
    }

    /**
     * @var null|TimetableDay
     */
    private $day;

    /**
     * @return TimetableDay|null
     */
    public function getDay(): ?TimetableDay
    {
        return $this->day;
    }

    /**
     * @param TimetableDay|null $day
     * @return TimetableDayDate
     */
    public function setDay(?TimetableDay $day): TimetableDayDate
    {
        $this->day = $day;
        return $this;
    }
}