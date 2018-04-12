<?php
namespace App\Timetable\Extension;

use Hillrange\Security\Util\UserTrackInterface;
use Hillrange\Security\Util\UserTrackTrait;

abstract class TimetableColumnExtension implements UserTrackInterface
{
    use UserTrackTrait;

    /**
     * @return bool
     */
    public function canDelete()
    {
        if ($this->getPeriods()->count() > 0)
            return false;

        return true;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->getName() . ' (' . $this->getCode() . ')';
    }
}