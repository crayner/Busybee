<?php
namespace App\Timetable\Entity;

use Hillrange\Security\Util\UserTrackInterface;
use Hillrange\Security\Util\UserTrackTrait;

abstract class TimetableDayExtension implements UserTrackInterface
{
    use UserTrackTrait;

    /**
     * @return bool
     */
    public function canDelete(): bool
    {
        return false;
    }
}