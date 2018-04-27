<?php
namespace App\Calendar\Entity;

use Hillrange\Security\Util\UserTrackInterface;
use Hillrange\Security\Util\UserTrackTrait;

class SpecialDayExtension implements UserTrackInterface
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
     * @return array
     */
    public function getTypeList(): array
    {
        return [
            'closure',
            'alter',
        ];
    }
}