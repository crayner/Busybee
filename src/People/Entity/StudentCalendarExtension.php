<?php
namespace App\People\Entity;

use Hillrange\Security\Util\UserTrackInterface;
use Hillrange\Security\Util\UserTrackTrait;

abstract class StudentCalendarExtension implements UserTrackInterface
{
    use UserTrackTrait;

    /**
     * @return string
     */
    public function getFullName(): string
    {
        if ($this->getCalendarGroup())
            return $this->getCalendarGroup()->getFullName();
        return '';
    }
}