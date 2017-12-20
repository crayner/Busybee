<?php
namespace App\Core\Listener;

use App\Entity\Setting;
use Hillrange\Security\Entity\User;
use Hillrange\Security\Repository\UserRepository;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ModifyRowListener implements EventSubscriberInterface
{
	/**
	 * @var null|User
	 */
	private $user;

	/**
	 * @var null|UserRepository
	 */
	private $userRepository;

	/**
	 * @var TokenStorage
	 */
	private $tokenStorage;

	/**
	 * ModifyRowListener constructor.
	 *
	 * @param myContainer $container
	 */
	public function __construct(TokenStorageInterface $tokenStorage, RequestStack $request)
	{
		$this->user      = null;
		$this->userRepository      = null;
		$this->tokenStorage = $tokenStorage;
		$this->session = is_null($request->getCurrentRequest()) ? null : $request->getCurrentRequest()->getSession();
	}

	/**
	 * @return array
	 */
	public static function getSubscribedEvents()
	{
		return array(
			'prePersist',
			'preUpdate'
		);
	}

	/**
	 * @param LifecycleEventArgs $args
	 */
	public function prePersist(LifecycleEventArgs $args)
	{
		$entity        = $args->getEntity();
		$entityManager = $args->getEntityManager();
		$this->userRepository = $entityManager->getRepository(User::class);

		if ($entity instanceof User && (! $this->getCurrentUser() instanceof User || intval($this->getCurrentUser()->getId()) == 0))
		{
			$entity->setCreatedOn(new \Datetime('now'));
			$entity->setCreatedBy($entity);
			$entity->setLastModified(new \Datetime('now'));
			$entity->setModifiedBy($entity);
		}
		elseif (! $this->getCurrentUser() instanceof User)
			trigger_error('No User Authenticated');
		else
		{
			$entity->setCreatedOn(new \Datetime('now'));
			$entity->setCreatedBy($this->getCurrentUser());
			$entity->setLastModified(new \Datetime('now'));
			$entity->setModifiedBy($this->getCurrentUser());
		}
		if (!is_null($entity->getCreatedBy()) && $entityManager->getUnitOfWork()->isScheduledForInsert($entity->getCreatedBy()))
		{
			$entityManager->detach($entity->getCreatedBy());
		}
		if (!is_null($entity->getModifiedBy()) && $entityManager->getUnitOfWork()->isScheduledForInsert($entity->getModifiedBy()))
		{
			$entityManager->detach($entity->getModifiedBy());
		}
	}

	/**
	 * @return User|null
	 */
	private function getCurrentUser(): ?User
	{
		if (! is_null($this->user)) return $this->user;
		$token = $this->tokenStorage->getToken();
		if (!is_null($token)) $this->user = $token->getUser();
		if (is_null($this->user))
		{
			$token   = unserialize($this->session->get('_security_default'));
			if ($token instanceof TokenInterface)
				$this->user = $token->getUser();
		}
		return $this->user instanceof User ? $this->user: null ;
	}

	/**
	 * @param PreUpdateEventArgs $args
	 *
	 * @throws \Exception
	 */
	public function preUpdate(PreUpdateEventArgs $args)
	{
		$entity        = $args->getEntity();
		$entityManager = $args->getEntityManager();;
		$entity->setLastModified(new \Datetime('now'));
		$entity->setModifiedBy($this->getCurrentUser());

		$x = (array) $entity;

		if (!is_null($entity->getModifiedBy()) && $entityManager->getUnitOfWork()->isScheduledForInsert($entity->getModifiedBy()))
		{
			$entityManager->detach($entity->getModifiedBy());
		}

		if ($entity instanceof Setting)
		{
			if ($entity->getSecurityActive())
				if (true !== $this->get('busybee_core_security.model.authorisation')->redirectAuthorisation($entity->getRole()->getRole()))
				{
					trigger_error('Settings cannot be updated without a user');
				}
		}
	}
}
