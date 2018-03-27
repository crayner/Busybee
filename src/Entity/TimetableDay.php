<?php
namespace App\Entity;

use App\Timetable\Entity\TimetableDayExtension;
use Hillrange\Form\Util\ColourManager;

class TimetableDay extends TimetableDayExtension
{
    /**
     * @var null|integer
     */
    private $id;

    /**
     * @return null|int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return TimetableDay
     */
    public function setId(?int $id): TimetableDay
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
     * @return TimetableDay
     */
    public function setName(?string $name): TimetableDay
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @var null|Timetable
     */
    private $timetable;

    /**
     * @return Timetable|null
     */
    public function getTimetable(): ?Timetable
    {
        return $this->timetable;
    }

    /**
     * @param Timetable|null $timetable
     * @return TimetableDay
     */
    public function setTimetable(?Timetable $timetable): TimetableDay
    {
        $this->timetable = $timetable;
        return $this;
    }

    /**
     * @var string|null
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
     * @return TimetableDay
     */
    public function setCode(?string $code): TimetableDay
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @var string|null
     */
    private $colour;

    /**
     * @return null|string
     */
    public function getColour(): ?string
    {
        return $this->colour;
    }

    /**
     * @param null|string $colour
     * @return TimetableDay
     */
    public function setColour(?string $colour): TimetableDay
    {
        $this->colour = ColourManager::formatColour($colour);

        return $this;
    }

    /**
     * @var string|null
     */
    private $fontColour;

    /**
     * @return null|string
     */
    public function getFontColour(): ?string
    {
        return $this->fontColour;
    }

    /**
     * @param null|string $fontColour
     * @return TimetableDay
     */
    public function setFontColour(?string $fontColour): TimetableDay
    {
        $this->fontColour = ColourManager::formatColour($fontColour);

        return $this;
    }

    /**
     * @return TimetableDay
     */
    public function checkColours(): TimetableDay
    {
        if ($this->getColour() != $this->getFontColour())
            return $this;

        return $this->setColour(null)
            ->setFontColour(null);
    }

    /**
     * @var integer|null
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
     * @return TimetableDay
     */
    public function setSequence(?int $sequence): TimetableDay
    {
        $this->sequence = $sequence;
        return $this;
    }

    /**
     * @var TimetableColumn|null
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
     * @return TimetableDay
     */
    public function setColumn(?TimetableColumn $column): TimetableDay
    {
        $this->column = $column;
        return $this;
    }
}