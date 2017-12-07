<?php

namespace App\Core\EntityExtension;

abstract class PageModel
{
	/**
	 * add Role
	 *
	 * @param $role
	 *
	 * @return PageModel
	 */
	public function addRole($role)
	{
		$roles = $this->getRoles();

		if (!empty($role) && !in_array($role, $roles))
		{
			$roles[] = $role;
			$this->setRoles(array_unique($roles));
			$this->setCacheTime(new \DateTime('-16 minutes'));
		}

		return $this;
	}

	public function roleToString()
	{
		return implode(',', $this->getRoles());
	}
}