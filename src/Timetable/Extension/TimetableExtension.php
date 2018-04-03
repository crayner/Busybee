<?php
namespace App\Timetable\Extension;

use Hillrange\Security\Util\UserTrackInterface;
use Hillrange\Security\Util\UserTrackTrait;

abstract class TimetableExtension implements UserTrackInterface
{
    use UserTrackTrait;
    /**
     * @var bool
     */
    protected $columnSort = false;

    /**
     * Get Full Name
     *
     * @return string
     */
    public function getFullName()
    {
        $calendar = is_null($this->getCalendar()) ? '' : $this->getCalendar()->getName();
        if (empty($this->getName()))
            return 'TimeTable';
        return $this->getName() . ' (' . $calendar . ')';
    }
}