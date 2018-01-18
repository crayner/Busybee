<?php
namespace App\Menu\Extension;

use App\Menu\Util\MenuManager;
use Twig\Extension\AbstractExtension;

class MenuExtension extends AbstractExtension
{
	/**
	 * @var MenuManager
	 */
	private $manager;

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'menu_extension';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFunctions()
	{
		return array(
			new \Twig_SimpleFunction('sectionMenuTest', array($this->manager, 'sectionMenuTest')),
		);
	}

	public function __construct(MenuManager $manager)
	{
		$this->manager = $manager;
	}
}