<?php
namespace App\Core\Extension;

use App\Core\Util\UserManager;
use App\People\Util\PersonManager;
use Twig\Extension\AbstractExtension;

class UserManagerExtension extends AbstractExtension
{
	/**
	 * @var UserManager
	 */
	private $userManager;

    /**
     * @var PersonManager
     */
	private $personManager;

	/**
	 * FormErrorsExtension constructor.
	 *
	 * @param UserManager $userManager
	 */
	public function __construct(UserManager $userManager, PersonManager $personManager)
	{
		$this->userManager = $userManager;
        $this->personManager = $personManager;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFunctions()
	{
		return [
			new \Twig_SimpleFunction('formatUserName', [$this->personManager, 'getFullUserName']),
			new \Twig_SimpleFunction('get_userManager', [$this, 'getUserManager']),
			new \Twig_SimpleFunction('get_CurrentCalendar', [$this->userManager, 'getCurrentCalendar']),
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