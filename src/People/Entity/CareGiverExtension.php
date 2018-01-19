<?php
namespace App\People\Entity;

use App\Entity\Person;
use Hillrange\Security\Util\UserTrackInterface;
use Hillrange\Security\Util\UserTrackTrait;

abstract class CareGiverExtension implements UserTrackInterface
{
	use UserTrackTrait;

	public function __construct()
	{
		$this->setPhoneContact(false);
		$this->setSmsContact(false);
		$this->setMailContact(false);
		$this->setEmailContact(false);
		$this->setContactPriority(0);
		$this->setRelationship('Unknown');
	}

	public function __toString()
	{
		return strval($this->getId());
	}

	public function formatName($options = []): string
	{
		if ($this->getPerson() instanceof Person)
		{
			return $this->getPerson()->formatName($options);
		}

		return '';
	}
}