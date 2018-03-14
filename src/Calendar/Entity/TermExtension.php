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
		return false;
	}

    /**
     * @return string
     */
    public function getLabel(): string
	{
		return $this->getName();
	}

    /**
     * @return string
     */
    public function getFullName(): string
    {
        $name = '';
        if ($this->getCalendar())
            $name = $this->getCalendar()->getName();
        $name = trim($name . ' ' . $this->getName());
        return $name;
    }
}