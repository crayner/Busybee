<?php
namespace App\School\Entity;

use Hillrange\Security\Util\UserTrackInterface;
use Hillrange\Security\Util\UserTrackTrait;

/**
 * Course
 */
class CourseExtension implements UserTrackInterface
{
	use UserTrackTrait;
    /**
     * @return string
     */
    public function getNameVersion()
    {
        return $this->getName() . ' ' . $this->getVersion();
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->getName() . ' (' . $this->getCode() . ') ' . $this->getVersion();
    }
}
