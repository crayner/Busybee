<?php
namespace App\Timetable\Extension;

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
            return '';
        return $this->getColumn()->getName() . ' - ' . $this->getFullName();
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->getName() . ' (' . $this->getNameShort() . ')';
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
    public function getMinutes()
    {
        $interval = ($this->getEnd()->getTimeStamp() - $this->getStart()->getTimeStamp()) / 60;

        return $interval;
    }
}