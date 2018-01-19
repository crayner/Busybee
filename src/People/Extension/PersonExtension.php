<?php
namespace App\People\Extension;

use App\Address\Util\AddressManager;
use App\Address\Util\PhoneManager;
use App\People\Util\PersonManager;
use Twig\Extension\AbstractExtension;

class PersonExtension extends AbstractExtension
{
	/**
	 * @var PersonManager
	 */
	private $personManager;

	/**
	 * @var AddressManager
	 */
	private $addressManager;

	/**
	 * @var PhoneManager
	 */
	private $phoneManager;

	/**
	 * PersonExtension constructor.
	 *
	 * @param SettingManager $sm
	 * @param PersonManager  $personManager
	 * @param AddressManager $addressManager
	 * @param PhoneManager   $phoneManager
	 */
	public function __construct(PersonManager $personManager, AddressManager $addressManager, PhoneManager $phoneManager)
	{
		$this->personManager  = $personManager;
		$this->addressManager = $addressManager;
		$this->phoneManager = $phoneManager;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFunctions()
	{
		return array(
			new \Twig_SimpleFunction('isCareGiver', array($this->personManager, 'isCareGiver')),
			new \Twig_SimpleFunction('isStudent', array($this->personManager, 'isStudent')),
			new \Twig_SimpleFunction('isStaff', array($this->personManager, 'isStaff')),
			new \Twig_SimpleFunction('isUser', array($this->personManager, 'isUser')),
			new \Twig_SimpleFunction('canBeStaff', array($this->personManager, 'canBeStaff')),
			new \Twig_SimpleFunction('canDeleteStaff', array($this->personManager, 'canDeleteStaff')),
			new \Twig_SimpleFunction('canBeCareGiver', array($this->personManager, 'canBeCareGiver')),
			new \Twig_SimpleFunction('canDeleteCareGiver', array($this->personManager, 'canDeleteCareGiver')),
			new \Twig_SimpleFunction('canBeStudent', array($this->personManager, 'canBeStudent')),
			new \Twig_SimpleFunction('canDeleteStudent', array($this->personManager, 'canDeleteStudent')),
			new \Twig_SimpleFunction('canBeUser', array($this->personManager, 'canBeUser')),
			new \Twig_SimpleFunction('canDeleteUser', array($this->personManager, 'canDeleteUser')),
			new \Twig_SimpleFunction('formatAddress', array($this->addressManager, 'formatAddress')),
			new \Twig_SimpleFunction('formatPhone', array($this->phoneManager, 'formatPhone')),
			new \Twig_SimpleFunction('validPerson', array($this->personManager, 'validPerson')),
		);
	}
}