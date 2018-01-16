<?php
namespace App\Core\Extension;

use App\Core\Manager\CalendarManager;
use App\Core\Manager\SettingManager;
use App\Core\Organism\Day;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Twig\Extension\AbstractExtension;

class CalendarExtension extends AbstractExtension
{
	/**
	 * @var Setting Manager
	 */
	private $sm;

	/**
	 * @var Calendar Manager
	 */
	private $cm;

	/**
	 * @var Container
	 */
	private $container;

	public function __construct(SettingManager $sm, CalendarManager $cm, Container $container)
	{
		$this->sm        = $sm;
		$this->cm        = $cm;
		$this->container = $container;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFunctions()
	{
		return array(
			new \Twig_SimpleFunction('get_dayClass', array($this->cm, 'getDayClass')),
			new \Twig_SimpleFunction('test_nextYear', array($this->cm, "testNextYear")),
		);
	}

	public function getName()
	{
		return 'calendar_twig_extension';
	}
}