<?php
namespace App\Timetable\Entity;

use App\Entity\Timetable;
use App\Entity\TimetablePeriod;
use Hillrange\Security\Util\UserTrackInterface;
use Hillrange\Security\Util\UserTrackTrait;

abstract class TimetablePeriodExtension implements UserTrackInterface
{
    use UserTrackTrait;
    /**
     * @return bool
     * @todo    Build canDelete Test for Period
     */
    public function canDelete()
    {
        return false;
    }

    /**
     * @var string|null
     */
    private $columnName = null;

    /**
     * @return mixed
     */
    public function getColumnName()
    {
        if ($this->columnName)
            return $this->columnName;

        if (is_null($this->getColumn()))
            return $this->columnName = 'ERROR '. $this->getFullName();

        return $this->columnName = $this->getColumn()->getName() . ' - ' . $this->getFullName();
    }

    /**
     * @var string|null
     */
    private $fullName = null;

    /**
     * @return string
     */
    public function getFullName()
    {
        if ($this->fullName)
            return $this->fullName;

        return $this->fullName = $this->getName() . ' (' . $this->getCode() . ')';
    }

    /**
     * @var null|string
     */
    private $startTime = null;

    /**
     * @return string
     */
    public function getStartTime($format = 'H:i')
    {
        if ($this->startTime)
            return $this->startTime;
        return $this->startTime = $this->getStart()->format($format);
    }

    /**
     * @var null|string
     */
    private $endTime = null;

    /**
     * @return string
     */
    public function getEndTime($format = 'H:i')
    {
        if ($this->endTime)
            return $this->endTime;

        return $this->endTime = $this->getEnd()->format($format);
    }

    /**
     * @var null|Timetable
     */
    private $timetable = null;

    public function getTimeTable()
    {
        if ($this->timetable)
            return $this->timetable;

        $col = $this->getColumn();
        if (is_null($col))
            return null;

        return $this->timetable = $col->getTimeTable();
    }

    /**
     * @var null|integer
     */
    private $minutes = null;

    /**
     * Get Minutes (interval)
     *
     * @return int
     */
    public function getMinutes(): int
    {
        if ($this->minutes)
            return $this->minutes;
        $this->minutes = ($this->getEnd()->getTimeStamp() - $this->getStart()->getTimeStamp()) / 60;

        return $this->minutes;
    }


    private static $periodTypes = [
        'students' => [
            'class',
            'pastoral',
        ],
        'no students' => [
            'break',
            'meeting',
        ],
    ];

    /**
     * @return array
     */
    public static function getPeriodTypeList(string $limit = '')
    {
        if (! in_array($limit, ['no students','students','','flat']))
            throw new \InvalidArgumentException('Dear Programmer: The list accepts only "no students, students, flat" or an empty string for Period Type List');

        if ($limit === 'no students')
            return self::$periodTypes['no students'];
        if ($limit === 'students')
            return self::$periodTypes['students'];
        if ($limit === 'flat')
            return array_merge(self::$periodTypes['students'], self::$periodTypes['no students']);
        return self::$periodTypes;
    }

    /**
     * @param TimetablePeriod|null $period
     * @return bool
     */
    public function isEqualTo(?TimetablePeriod $period): bool
    {
        if ($this->getId() === $period->getId())
            return true;

        return false;
    }

    /**
     * isBreak
     *
     * @return bool
     */
    public function isBreak(): bool
    {
        if ($this->getPeriodType() === 'break')
            return true;
        return false;
    }

    /**
     * hasActivities
     *
     * @return bool
     */
    public function hasActivities(): bool
    {
        if ($this->getActivities()->count() > 0)
            return true;
        return false;
    }

    /**
     * @var string
     */
    private $class;

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class ?: '';
    }

    /**
     * @param string $class
     * @return TimetablePeriodExtension
     */
    public function setClass(string $class): TimetablePeriodExtension
    {
        $this->class = $class;
        return $this;
    }

    /**
     * serialise
     *
     * @return string
     */
    public function serialise()
    {
        return serialize([
            $this->getId(),
            $this->getName(),
            $this->getCode(),
            $this->getStart(),
            $this->getEnd(),
            $this->getPeriodType(),
            $this->getActivities(),
            $this->getColumn(),
            $this->getColumnName(),
        ]);
    }

    /**
     * unserialise
     *
     * @param $serialised
     * @return $this
     */
    public function unserialise($serialised)
    {
        list(
            $id,
            $name,
            $code,
            $start,
            $end,
            $periodType,
            $activities,
            $column,
            $this->columnName,
            ) = unserialize($serialised);

        $this->setId($id)
            ->setName($name)
            ->setCode($code)
            ->setStart($start)
            ->setEnd($end)
            ->setPeriodType($periodType)
            ->setActivities($activities)
            ->setColumn($column);

        return $this;
    }
}