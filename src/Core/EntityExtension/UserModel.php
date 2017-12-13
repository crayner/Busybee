<?php
namespace App\Core\EntityExtension;

use App\Entity\User;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Yaml\Yaml;

/**
 *
 */
abstract class UserModel implements AdvancedUserInterface, \Serializable
{
	/**
	 * @var string
	 */
	protected $plainPassword;

	/**
	 * @var array
	 */
	protected $roles;

	/**
	 * @var bool
	 */
	protected $installer = false;

	public function __construct()
	{
		$this->roles = [];
		$this->setLocked(false);
		$this->setEnabled(false);
		$this->setExpired(false);
		$this->setCredentialsExpired(false);
		$this->setLocale('en_GB');
		$this->setPassword('This password will never work.');
	}

	public function getPlainPassword()
	{
		return $this->plainPassword;
	}

	public function setPlainPassword($password)
	{

		$this->plainPassword = $password;

		return $this;
	}

	public function getSalt()
	{
		return null;
	}

	public function isSuperAdmin()
	{
		return $this->hasRole(static::ROLE_SYSTEM_ADMIN);
	}

	public function setSuperAdmin($boolean)
	{

		if (true === $boolean)
			$this->addRole(static::ROLE_SYSTEM_ADMIN);
		else
			$this->removeRole(static::ROLE_SYSTEM_ADMIN);

		return $this;
	}

	public function isPasswordRequestNonExpired($ttl)
	{

		return $this->getPasswordRequestedAt() instanceof \DateTime && $this->getPasswordRequestedAt()->getTimestamp() + $ttl > time();
	}

	public function isAccountNonExpired()
	{
		if (true === $this->expired)
		{
			return false;
		}

		if (null !== $this->expiresAt && $this->expiresAt->getTimestamp() < time())
		{
			return false;
		}

		return true;
	}

	public function isAccountNonLocked()
	{
		return !$this->locked;
	}

	public function isCredentialsNonExpired()
	{
		return !$this->credentialsExpired;
	}

	public function isEnabled()
	{
		return $this->enabled;
	}

	/**
	 * Removes sensitive data from the user.
	 */
	public function eraseCredentials()
	{
		$this->plainPassword = null;
	}

	/**
	 * Serializes the user.
	 *
	 * The serialized data have to contain the fields used by the equals method and the username.
	 *
	 * @return string
	 */
	public function serialize()
	{
		return serialize(array(
			$this->password,
			$this->usernameCanonical,
			$this->username,
			$this->expired,
			$this->locked,
			$this->credentialsExpired,
			$this->enabled,
			$this->id,
		));
	}

	/**
	 * Unserializes the user.
	 *
	 * @param string $serialized
	 */
	public function unserialize($serialized)
	{
		$data = unserialize($serialized);
		// add a few extra elements in the array to ensure that we have enough keys when unserializing
		// older data which does not include all properties.
		$data = array_merge($data, array_fill(0, 2, null));

		list(
			$this->password,
			$this->usernameCanonical,
			$this->username,
			$this->expired,
			$this->locked,
			$this->credentialsExpired,
			$this->enabled,
			$this->id
			) = $data;
	}

	/**
	 * @return bool
	 */
	public function getChangePassword()
	{
		return $this->getCredentialsExpired();
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->getId() . '-' . $this->getUsername();
	}

	/**
	 * @return bool
	 */
	public function canDelete()
	{
		if ($this->getId() == 1)
			return false;

		if ($this->enabled)
			return false;

		return true;
	}

	public function rolesToString()
	{
		$roles = $this->getRoles();

		return implode(', ', $roles);
	}

	/**
	 * Returns the roles granted to the user.
	 *
	 * <code>
	 * public function getRoles()
	 * {
	 *     return array('ROLE_USER');
	 * }
	 * </code>
	 *
	 * Alternatively, the roles might be stored on a ``roles`` property,
	 * and populated in any number of different ways when the user object
	 * is created.
	 *
	 * @return Role[] The user roles
	 */
	public function getRoles()
	{
		if (!empty($this->roles))
			return $this->roles;
		$this->roles = array();

		$groups = $this->getGroups();

		$groupData = $this->getGroupList();

		foreach ($groups as $group)
		{
			$roles = $groupData[$group];
			foreach ($roles as $role)
			{
				$this->roles[] = $role;
			}
		}
		foreach ($this->getDirectroles() as $role)
			$this->roles = array_merge($this->roles, array($role));

		return $this->roles;
	}

	public function getGroupList()
	{
		try
		{
			$groups = Yaml::parse(file_get_contents('../src/Busybee/Core/SecurityBundle/Resources/config/services.yml'));
			$groups = $groups['parameters']['groups'];
		}
		catch (Exception $e)
		{
			return array();
		}

		return $groups;

	}

	/**
	 * @return bool
	 */
	public function isInstaller(): bool
	{
		return $this->installer;
	}

	/**
	 * @param $installer
	 *
	 * @return User
	 */
	public function setInstaller($installer): User
	{
		$this->installer = $installer;

		return $this;
	}

	/**
	 * Format Name
	 * @return string
	 */
	public function formatName()
	{
		return $this->getUsername();
	}
}
