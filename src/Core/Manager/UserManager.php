<?php
namespace App\Core\Manager;

use App\Core\Definition\CanonicaliserInterface;
use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserManager implements UserProviderInterface
{
	/**
	 * @var EncoderFactoryInterface
	 */
	protected $encoderFactory;

	/**
	 * @var CanonicaliserInterface
	 */
	protected $usernameCanonicaliser;

	/**
	 * @var CanonicaliserInterface
	 */
	protected $emailCanonicaliser;

	/**
	 * @var ObjectManager
	 */
	protected $objectManager;

	/**
	 * @var string
	 */
	protected $class;

	/**
	 * @var \Doctrine\Common\Persistence\ObjectRepository
	 */
	protected $repository;

	/**
	 * @var Session
	 */
	protected $session;

	/**
	 * @var Person
	 */
	private $person;

	/**
	 * Constructor.
	 *
	 * @param EncoderFactoryInterface $encoderFactory
	 * @param CanonicaliserInterface  $canonicaliser
	 * @param ObjectManager           $om
	 * @param string                  $class
	 */
	public function __construct(EncoderFactoryInterface $encoderFactory,  CanonicaliserInterface $canonicaliser, ObjectManager $om, $class = User::class)
	{
		$this->encoderFactory        = $encoderFactory;
		$this->usernameCanonicaliser = $canonicaliser;
		$this->emailCanonicaliser    = $canonicaliser;

		$this->objectManager = $om;
		$this->session       = new Session();

		$this->repository = $om->getRepository($class);

		$metadata    = $om->getClassMetadata($class);
		$this->class = $metadata->getName();
	}

	/**
	 * {@inheritDoc}
	 */
	public function deleteUser(UserInterface $user)
	{
		$this->objectManager->remove($user);
		$this->objectManager->flush();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getClass()
	{
		return $this->class;
	}

	/**
	 * {@inheritDoc}
	 */
	public function findUserBy(array $criteria)
	{
		return $this->repository->findOneBy($criteria);
	}

	/**
	 * {@inheritDoc}
	 */
	public function reloadUser(UserInterface $user)
	{
		$this->objectManager->refresh($user);
	}

	/**
	 * Updates a user.
	 *
	 * @param UserInterface $user
	 * @param Boolean       $andFlush Whether to flush the changes (default true)
	 */
	public function updateUser(UserInterface $user, $andFlush = true)
	{
		$this->updateCanonicalFields($user);
		$this->updatePassword($user);

		$this->objectManager->persist($user);
		if ($andFlush)
		{
			$this->objectManager->flush();
		}
	}

	/**
	 * Find Children
	 *
	 * @param $user
	 * @param $checker
	 *
	 * @return array
	 */
	public function findChildren($user, $checker)
	{

		$users = array();
		foreach ($this->findUsers() as $test)
		{
			if ($test->getUsername() === $user->getUsername())
				continue;
			$valid = true;
			$roles = $test->getRoles();

			foreach ($roles as $role)
			{
				if (!$checker->isGranted($role))
				{
					$valid = false;
					break;
				}
			}
			if (!$valid)
				continue;
			$users[] = $test;
		}
		$x = array();
		foreach ($users as $w)
		{
			$x[$w->getUsername()]['name']  = $w->getUsername();
			$x[$w->getUsername()]['id']    = $w->getID();
			$x[$w->getUsername()]['email'] = $w->getEmail();
		}
		ksort($x);

		return $x;
	}

	/**
	 * {@inheritDoc}
	 */
	public function findUsers()
	{
		return $this->repository->findAll();
	}

	/**
	 * @return Session
	 */
	public function getSession(): Session
	{
		return $this->session;
	}

	/**
	 * @return User
	 */
	public function getCurrentUser()
	{
		$security = unserialize($this->getSession()->get('_security_default'));

		return $security->getUser();
	}

	/**
	 * Person Exists
	 *
	 * @param User|null $entity
	 *
	 * @return bool|int
	 */
	public function personExists(User $entity = null)
	{
		if (class_exists('Busybee\People\PersonBundle\Model\PersonManager'))
		{
			$metaData = $this->objectManager->getClassMetadata('Busybee\People\PersonBundle\Entity\Person');
			$schema   = $this->objectManager->getConnection()->getSchemaManager();

			if (is_null($entity))
				$entity = $this->getCurrentUser();

			if ($schema->tablesExist([$metaData->getTableName()]))
			{
				$this->person = $this->objectManager->getRepository(Person::class)->findOneByUser($entity);

				if ($this->person instanceof Person)
					return $this->person->getId();
			}
		}

		$this->person = null;

		return false;
	}

	/**
	 * Get Person
	 *
	 * @return Person
	 */
	public function getPerson()
	{
		return $this->person;
	}

	/**
	 * Returns an empty user instance
	 *
	 * @return UserInterface
	 */
	public function createUser()
	{
		$class = $this->getClass();
		$user  = new $class;

		return $user;
	}

	/**
	 * Finds a user either by email, or username
	 *
	 * @param string $usernameOrEmail
	 *
	 * @return UserInterface
	 */
	public function findUserByUsernameOrEmail($usernameOrEmail)
	{
		if (filter_var($usernameOrEmail, FILTER_VALIDATE_EMAIL))
		{
			return $this->findUserByEmail($usernameOrEmail);
		}

		return $this->findUserByUsername($usernameOrEmail);
	}

	/**
	 * Finds a user by email
	 *
	 * @param string $email
	 *
	 * @return UserInterface
	 */
	public function findUserByEmail($email)
	{
		return $this->findUserBy(['emailCanonical' => $this->canonicaliseEmail($email)]);
	}

	/**
	 * Canonicalises an email
	 *
	 * @param string $email
	 *
	 * @return string
	 */
	protected function canonicaliseEmail($email)
	{
		return $this->emailCanonicaliser->canonicalise($email);
	}

	/**
	 * Finds a user by username
	 *
	 * @param string $username
	 *
	 * @return UserInterface
	 */
	public function findUserByUsername($username)
	{
		return $this->findUserBy(array('usernameCanonical' => $this->canonicaliseUsername($username)));
	}

	/**
	 * Canonicalises a username
	 *
	 * @param string $username
	 *
	 * @return string
	 */
	protected function canonicaliseUsername($username)
	{
		return $this->usernameCanonicaliser->canonicalise($username);
	}

	/**
	 * Finds a user either by confirmation token
	 *
	 * @param string $token
	 *
	 * @return UserInterface
	 */
	public function findUserByConfirmationToken($token)
	{
		return $this->findUserBy(array('confirmationToken' => $token));
	}

	/**
	 * Refreshed a user by User Instance
	 *
	 * Throws UnsupportedUserException if a User Instance is given which is not
	 * managed by this UserManager (so another Manager could try managing it)
	 *
	 * It is strongly discouraged to use this method manually as it bypasses
	 * all ACL checks.
	 *
	 * @deprecated Use Busybee\Core\SecurityBundle\Security\UserProvider instead
	 *
	 * @param SecurityUserInterface $user
	 *
	 * @return UserInterface
	 */
	public function refreshUser(UserInterface $user)
	{
		trigger_error('Using the UserManager as user provider is deprecated. Use Busybee\Core\SecurityBundle\Security\UserProvider instead.', E_USER_DEPRECATED);

		$class = $this->getClass();
		if (!$user instanceof $class)
		{
			throw new UnsupportedUserException('Account is not supported.');
		}
		if (!$user instanceof User)
		{
			throw new UnsupportedUserException(sprintf('Expected an instance of Busybee\Core\SecurityBundle\Model\User, but got "%s".', get_class($user)));
		}

		$refreshedUser = $this->findUserBy(array('id' => $user->getId()));
		if (null === $refreshedUser)
		{
			throw new UsernameNotFoundException(sprintf('User with ID "%d" could not be reloaded.', $user->getId()));
		}

		return $refreshedUser;
	}

	/**
	 * Loads a user by username
	 *
	 * It is strongly discouraged to call this method manually as it bypasses
	 * all ACL checks.
	 *
	 * @deprecated Use Busybee\Core\SecurityBundle\Security\UserProvider instead
	 *
	 * @param string $username
	 *
	 * @return UserInterface
	 */
	public function loadUserByUsername($username)
	{
		trigger_error('Using the UserManager as user provider is deprecated. Use Busybee\Core\SecurityBundle\Security\UserProvider instead.', E_USER_DEPRECATED);

		$user = $this->findUserByUsername($username);

		if (!$user)
		{
			throw new UsernameNotFoundException(sprintf('No user with name "%s" was found.', $username));
		}

		return $user;
	}

	/**
	 * {@inheritDoc}
	 */
	public function updateCanonicalFields(UserInterface $user)
	{
		$user->setUsernameCanonical($this->canonicaliseUsername($user->getUsername()));
		$user->setEmailCanonical($this->canonicaliseEmail($user->getEmail()));
	}

	/**
	 * {@inheritDoc}
	 */
	public function updatePassword(UserInterface $user)
	{
		if (0 !== strlen($password = $user->getPlainPassword()))
		{
			$encoder = $this->getEncoder($user);
			$user->setPassword($encoder->encodePassword($password, $user->getSalt()));
			$user->eraseCredentials();
		}
	}

	protected function getEncoder(UserInterface $user)
	{
		return $this->encoderFactory->getEncoder($user);
	}

	/**
	 * {@inheritDoc}
	 * @deprecated UseBusybee\Core\SecurityBundle\Security\UserProvider instead
	 */
	public function supportsClass($class)
	{
		trigger_error('Using the UserManager as user provider is deprecated. Use Busybee\Core\SecurityBundle\Security\UserProvider instead.', E_USER_DEPRECATED);

		return $class === $this->getClass();
	}

	/**
	 * @param   UserInterface $user
	 *
	 * @return  Year
	 */
	public function getSystemYear(UserInterface $user)
	{
		if ($this->getSession()->has('currentYear') && $this->getSession()->get('currentYearCache', new \DateTime('-15 minutes') > new \DateTime('-10 Minutes')))
		{
			return $this->getSession()->get('currentYear');
		}
		if (!is_null($user->getYear()) && $user->getYear() instanceof Year)
		{
			$user->setYear($this->objectManager->getRepository(Year::class)->find($user->getYear()->getId()));
			$this->getSession()->set('currentYear', $user->getYear());
			$this->getSession()->set('currentYearCache', new \DateTime());

			return $user->getYear();
		}
		$year = $this->objectManager->getRepository(Year::class)->findCurrentYear();

		if (!empty($year->getId()))
		{
			$this->getSession()->set('currentYear', $year);
			$this->getSession()->set('currentYearCache', new \DateTime());
		}

		return $year;
	}

	/**
	 * @param $id
	 *
	 * @return mixed
	 */
	public function find($id)
	{
		return $this->objectManager->getRepository(User::class)->find($id);
	}


	/**
	 * @param User $person
	 *
	 * @return bool
	 */
	public function canDeleteUser(User $user = null): bool
	{

		if (is_null($user))
			return false;

		//Place rules here to stop delete .
		if (!$user instanceof User)
			return false;

		if (in_array('ROLE_SYSTEM_ADMIN', $user->getRoles()))
			return false;


		return $user->canDelete();
	}

	/**
	 * Format User Name
	 *
	 * @param User $user
	 *
	 * @return string
	 */
	public function formatUserName(User $user)
	{
		if ($this->personExists($user))
			return $this->getPerson()->formatName();

		return $user->formatName();
	}}
