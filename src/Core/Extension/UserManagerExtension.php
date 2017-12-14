<?php
namespace App\Core\Extension;

use HillRange\Security\Manager\UserManager;
use Twig\Extension\AbstractExtension;

class UserManagerExtension extends AbstractExtension
{
	/**
	 * @var UserManager
	 */
	private $userManager;

	/**
	 * FormErrorsExtension constructor.
	 *
	 * @param UserManager $userManager
	 */
	public function __construct(UserManager $userManager)
	{
		$this->userManager = $userManager;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFunctions()
	{
		return [
			new \Twig_SimpleFunction('formatUserName', [$this->userManager, 'formatUserName']),
			new \Twig_SimpleFunction('get_userManager', [$this, 'getUserManager']),
			new \Twig_SimpleFunction('get_SystemYear', [$this->userManager, 'getSystemYear']),
		];
	}

	/**
	 * Get User Manager
	 *
	 * @return  UserManager
	 */
	public function getUserManager()
	{
		return $this->userManager;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'user_manager_extension';
	}
}