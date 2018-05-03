<?php
namespace App\School\Entity;

use Hillrange\Security\Util\UserTrackInterface;
use Hillrange\Security\Util\UserTrackTrait;

abstract class SpaceExtension implements UserTrackInterface
{
	use UserTrackTrait;

    /**
     * @return string
     */
    public function getNameCapacity()
    {
        return $this->getName() . ' (' . $this->getCapacity() . ')';
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->getCampus()->getName() . ' - ' . $this->getNameCapacity();
    }
}