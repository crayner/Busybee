<?php
namespace App\People\Entity;

use App\Entity\DepartmentMember;
use App\Entity\Person;
use Hillrange\Security\Util\SingleTableChildInterface;

abstract class StaffExtension extends Person
{
	/**
	 * @param string $float
	 *
	 * @return mixed
	 */
	public function getPortrait($float = 'none')
	{
		return $this->getPerson()->getPhoto75($float);
	}

	/**
	 * @return string
	 */
	public function getDepartments()
	{
		$depts  = $this->getDepartment();
		$string = '';
		foreach ($depts as $dept)
		{
			if ($dept instanceof DepartmentMember)
				$string .= $dept->getDepartment()->getName() . ', ';
		}

		return trim($string, ', ');
	}

	/**
	 * @todo Check if a Staff record can be deleted
	 */
	public function canDelete()
	{
		// Add Staff Delete checks here.

		return parent::canDelete();
	}
}