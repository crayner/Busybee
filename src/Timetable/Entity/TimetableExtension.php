<?php
namespace App\Timetable\Entity;

use App\Entity\Timetable;
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

    /**
     * @param Timetable $timetable
     * @return bool
     */
    public function isEqualTo(Timetable $timetable): bool
    {
        if ($this !== $timetable)
            return false;

        if ($this->getLastModified() === $timetable->getLastModified())
            return false;

        return true;
    }
}