<?php
namespace App\People\Entity;

use Hillrange\Security\Util\UserTrackInterface;
use Hillrange\Security\Util\UserTrackTrait;

abstract class FamilyExtension implements UserTrackInterface
{
	use UserTrackTrait;

	public function checkFamilyName()
	{
		if (empty($this->getName()))
		{
			$cgs = $this->getCaregivers();

			$cg1 = $cgs->get(0);
			$cg2 = $cgs->containsKey(1) ? $this->getCaregivers()->get(1) : null;

			$name = $cg1->formatName(['preferredOnly' => true]);

			if ($cg2 instanceof CareGiver)
			{
				$name2   = $cg2->formatName(['preferredOnly' => true]);
				$surname = substr($name, 0, strpos($name, ':') + 1);
				$name2   = trim(str_replace($surname, '', $name2));
				if (!empty($name2))
					$name .= ' & ' . $name2;
			}

			if (empty($name))
				$name = null;

			$this->setName($name);

			return $name;
		}
	}
}