<?php
namespace App\Core\Extension;

use App\Calendar\Util\CalendarManager;
use App\Core\Manager\SettingManager;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Twig\Extension\AbstractExtension;

class CalendarExtension extends AbstractExtension
{
	/**
	 * @var CalendarManager
	 */
	private $calendarManager;

    /**
     * CalendarExtension constructor.
     * @param SettingManager $sm
     * @param CalendarManager $calendarManager
     * @param Container $container
     */
    public function __construct(CalendarManager $calendarManager)
	{
		$this->calendarManager        = $calendarManager;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFunctions()
	{
		return array(
			new \Twig_SimpleFunction('get_dayClass', array($this->calendarManager, 'getDayClass')),
			new \Twig_SimpleFunction('test_nextYear', array($this->calendarManager, "testNextYear")),
		);
	}

    /**
     * @return string
     */
    public function getName()
	{
		return 'calendar_twig_extension';
	}
}