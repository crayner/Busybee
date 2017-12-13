<?php
namespace App\Core\Provider;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface as SecurityUserInterface;

class UserProvider implements UserProviderInterface
{
	/**
	 * @var UserManagerInterface
	 */
	protected $userManager;

	/**
	 * Constructor.
	 *
	 * @param UserManagerInterface $userManager
	 */
	public function __construct(UserManagerInterface $userManager)
	{
		$this->userManager = $userManager;
	}

	/**
	 * {@inheritDoc}
	 */
	public function loadUserByUsername($username)
	{
		$user = $this->findUser($username);

		if (!$user)
		{
			throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
		}

		return $user;
	}

	/**
	 * Finds a user by username.
	 *
	 * This method is meant to be an extension point for child classes.
	 *
	 * @param string $username
	 *
	 * @return UserInterface|null
	 */
	protected function findUser($username)
	{
		return $this->userManager->findUserByUsername($username);
	}

	/**
	 * {@inheritDoc}
	 */
	public function refreshUser(SecurityUserInterface $user)
	{
		if (!$user instanceof User && !$user instanceof PropelUser)
		{
			throw new UnsupportedUserException(sprintf('Expected an instance of Busybee\Core\SecurityBundle\Entity\User, but got "%s".', get_class($user)));
		}

		if (!$this->supportsClass(get_class($user)))
		{
			throw new UnsupportedUserException(sprintf('Expected an instance of %s, but got "%s".', $this->userManager->getClass(), get_class($user)));
		}

		if (null === $reloadedUser = $this->userManager->findUserBy(array('id' => $user->getId())))
		{
			throw new UsernameNotFoundException(sprintf('User with ID "%d" could not be reloaded.', $user->getId()));
		}

		return $reloadedUser;
	}

	/**
	 * {@inheritDoc}
	 */
	public function supportsClass($class)
	{
		$userClass = $this->userManager->getClass();

		return $userClass === $class || is_subclass_of($class, $userClass);
	}

	/**
	 * Find
	 *
	 * @param $id
	 *
	 * @return mixed
	 */
	public function find($id)
	{
		$user = $this->userManager->find($id);
		if (!$user)
		{
			throw new UsernameNotFoundException(sprintf('User with ID "%d" was not found.', $id));
		}

		return $user;
	}

	/**
	 * Get User Manager
	 *
	 * @return UserManager
	 */
	public function getUserManager(): UserManager
	{
		return $this->userManager;
	}

	/**
	 * Get Current User
	 *
	 * @return \Busybee\Core\SecurityBundle\Entity\User
	 */
	public function getCurrentUser()
	{
		return $this->getUserManager()->getCurrentUser();
	}
}
