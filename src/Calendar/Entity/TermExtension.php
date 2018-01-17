<?php
namespace App\Calendar\Entity;

use Hillrange\Security\Util\UserTrackInterface;
use Hillrange\Security\Util\UserTrackTrait;

abstract class TermExtension implements UserTrackInterface
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

	public function getLabel()
	{
		return $this->getName();
	}
}