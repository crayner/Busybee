<?php
namespace App\Entity;

use Hillrange\Security\Util\UserTrackInterface;
use Hillrange\Security\Util\UserTrackTrait;

class TimetableColumnPeriod implements UserTrackInterface
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
     * @return TimetableColumnPeriod
     */
    public function setId(?int $id): TimetableColumnPeriod
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
     * @return TimetableColumnPeriod
     */
    public function setName(?string $name): TimetableColumnPeriod
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
     * @return TimetableColumnPeriod
     */
    public function setCode(?string $code): TimetableColumnPeriod
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @var null|\DateTime
     */
    private $timeStart;

    /**
     * @return \DateTime|null
     */
    public function getTimeStart(): ?\DateTime
    {
        return $this->timeStart;
    }

    /**
     * @param \DateTime|null $timeStart
     * @return TimetableColumnPeriod
     */
    public function setTimeStart(?\DateTime $timeStart): TimetableColumnPeriod
    {
        $this->timeStart = $timeStart;
        return $this;
    }

    /**
     * @var null|\DateTime
     */
    private $timeEnd;

    /**
     * @return \DateTime|null
     */
    public function getTimeEnd(): ?\DateTime
    {
        return $this->timeEnd;
    }

    /**
     * @param \DateTime|null $timeEnd
     * @return TimetableColumnPeriod
     */
    public function setTimeEnd(?\DateTime $timeEnd): TimetableColumnPeriod
    {
        $this->timeEnd = $timeEnd;
        return $this;
    }

    /**
     * @var null|string
     */
    private $type;

    /**
     * @return null|string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param null|string $type
     * @return TimetableColumnPeriod
     */
    public function setType(?string $type): TimetableColumnPeriod
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @var null|TimetableColumn
     */
    private $column;

    /**
     * @return TimetableColumn|null
     */
    public function getColumn(): ?TimetableColumn
    {
        return $this->column;
    }

    /**
     * @param TimetableColumn|null $column
     * @return TimetableColumnPeriod
     */
    public function setColumn(?TimetableColumn $column): TimetableColumnPeriod
    {
        $this->column = $column;
        return $this;
    }
}