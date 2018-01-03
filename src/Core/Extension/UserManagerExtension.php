<?php
namespace App\Core\Extension;

use App\Core\Util\UserManager;
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
			new \Twig_SimpleFunction('get_SystemCalendar', [$this->userManager, 'getSystemCalendar']),
			new \Twig_SimpleFunction('get_UserSetting', [$this->userManager, 'getUserSetting']),
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