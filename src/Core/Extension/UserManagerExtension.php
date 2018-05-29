<?php
namespace App\Core\Extension;

use App\Calendar\Util\CalendarManager;
use App\Core\Util\UserManager;
use App\Entity\Calendar;
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
            new \Twig_SimpleFunction('get_CurrentCalendar', [$this, 'getCurrentCalendar']),
            new \Twig_SimpleFunction('get_CurrentCalendarName', [$this, 'getCurrentCalendarName']),
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

    /**
     * getCurrentCalendar
     *
     * @return Calendar|null
     */
    public function getCurrentCalendar(): ?Calendar
    {
        return CalendarManager::getCurrentCalendar();
    }

    /**
     * getCurrentCalendarName
     *
     * @return string
     */
    public function getCurrentCalendarName(): string
    {
        if ($this->getCurrentCalendar() instanceof Calendar)
            return $this->getCurrentCalendar()->getName();
        return 'Unknown';
    }
}