<?php
namespace App\School\Entity;

use Hillrange\Security\Util\UserTrackInterface;
use Hillrange\Security\Util\UserTrackTrait;

abstract class SpaceExtension implements UserTrackInterface
{
	use UserTrackTrait;

    public function getNameCapacity()
    {
        return $this->getName() . ' (' . $this->getCapacity() . ')';
    }
}