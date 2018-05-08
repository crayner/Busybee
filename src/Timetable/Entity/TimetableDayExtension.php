<?php
namespace App\Timetable\Entity;

use Hillrange\Security\Util\UserTrackInterface;
use Hillrange\Security\Util\UserTrackTrait;

abstract class TimetableDayExtension implements UserTrackInterface
{
    use UserTrackTrait;

    /**
     * TimetableDayExtension constructor.
     */
    public function __construct()
    {
        $this->setDayType(true);
    }
}