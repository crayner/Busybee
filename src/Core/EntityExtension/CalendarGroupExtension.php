<?php
namespace App\Core\EntityExtension;

use Hillrange\Security\Util\UserTrackInterface;
use Hillrange\Security\Util\UserTrackTrait;

abstract class CalendarGroupExtension implements UserTrackInterface
{
	use UserTrackTrait;
	/**
	 * Can Delete
	 *
	 * @return bool
	 */
	public function canDelete()
	{
		return true;
	}

	/**
	 * Get Full Name
	 *
	 * @return string
	 */
	public function getFullName()
	{
		return $this->getCalendarGroupYear();
	}

	/**
	 * Get Calendar Group Year
	 *
	 * @return string
	 */
	public function getCalendarGroupYear()
	{
		return $this->getNameShort() . ' (' . $this->getYear()->getName() . ')';
	}
}