<?php
namespace App\People\Entity;

use App\Entity\Person;

abstract class ContactExtension extends Person
{
	/**
	 * Can Delete
	 *     * @todo Check if a Contact record can be deleted
	 * @return  bool
	 */
	public function canDelete(): bool
	{
		return parent::canDelete();
	}
}