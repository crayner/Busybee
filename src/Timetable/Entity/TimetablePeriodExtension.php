<?php
namespace App\Timetable\Entity;

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
     * @return mixed
     */
    public function getColumnName()
    {
        if (is_null($this->getColumn()))
            return 'ERROR '. $this->getFullName();
        return $this->getColumn()->getName() . ' - ' . $this->getFullName();
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->getName() . ' (' . $this->getCode() . ')';
    }

    /**
     * @return string
     */
    public function getStartTime($format = 'H:i')
    {
        return $this->getStart()->format($format);
    }

    /**
     * @return string
     */
    public function getEndTime($format = 'H:i')
    {
        return $this->getEnd()->format($format);
    }

    public function getTimeTable()
    {
        $col = $this->getColumn();
        if (is_null($col))
            return null;

        return $col->getTimeTable();
    }

    /**
     * Get Minutes (interval)
     *
     * @return int
     */
    public function getMinutes(): int
    {
        $interval = ($this->getEnd()->getTimeStamp() - $this->getStart()->getTimeStamp()) / 60;

        return $interval;
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
}