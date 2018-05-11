<?php
namespace App\People\Entity;

use App\Entity\Calendar;
use Hillrange\Security\Util\UserTrackInterface;
use Hillrange\Security\Util\UserTrackTrait;

abstract class RollGroupExtension implements UserTrackInterface
{
	use UserTrackTrait;

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return $this->getName() . ' ('.$this->getcode().') - in ' . $this->getCalendar()->getName();
    }

	/**
	 * @return bool
	 */
	public function canDelete(): bool
	{
		return true;
	}
}