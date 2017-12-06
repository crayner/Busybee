<?php
namespace App\Core\Extension;

use App\Core\Manager\MenuManager;
use App\Core\Manager\SettingManager;
use Twig\Extension\AbstractExtension;

class SettingExtension extends AbstractExtension
{
	/**
	 * @var SettingManager
	 */
	private $settingManager;

	/**
	 * @var MenuManager
	 */
	private $menuManager;

	public function __construct(SettingManager $sm, MenuManager $menuManager)
	{
		$this->settingManager   = $sm;
		$this->menuManager      = $menuManager;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFunctions()
	{
		return array(
			new \Twig_SimpleFunction('get_setting', array($this->settingManager, 'get')),
			new \Twig_SimpleFunction('get_parameter', array($this, 'getParameter')),
			new \Twig_SimpleFunction('get_menu', array($this, 'getMenu')),
			new \Twig_SimpleFunction('get_menuItems', array($this, 'getMenuItems')),
			new \Twig_SimpleFunction('test_menuItem', array($this, 'testMenuItem')),
			new \Twig_SimpleFunction('menu_required', array($this, 'menuRequired')),
			new \Twig_SimpleFunction('array_flip', array($this, 'arrayFlip')),
			new \Twig_SimpleFunction('get_section', array($this->menuManager, 'getSection')),
		);
	}

	/**
	 * @param      $name
	 * @param null $default
	 *
	 * @return mixed
	 */
	public function getParameter($name, $default = null)
	{
		if (strpos($name, '.') === false)
			return $this->settingManager->getParameter($name, $default);
		$name  = explode('.', $name);
		$value = $this->settingManager->getParameter($name[0]);
		array_shift($name);
		while (!empty($name))
		{
			$key   = reset($name);
			$value = $value[$key];
			array_shift($name);
		}

		return $value;
	}

	/**
	 * @return mixed
	 */
	public function getMenu()
	{
		return $this->menuManager->getMenu();
	}

	/**
	 * @param $node
	 *
	 * @return mixed
	 */
	public function getMenuItems($node)
	{
		return $this->menuManager->getMenuItems($node);
	}

	/**
	 * @param $test
	 *
	 * @return bool
	 */
	public function testMenuItem($test)
	{
		return $this->menuManager->testMenuItem($test);
	}

	/**
	 * @param $menu
	 *
	 * @return bool
	 */
	public function menuRequired($menu)
	{
		return $this->menuManager->menuRequired($menu);
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'system_twig_extension';
	}

	public function arrayFlip($data)
	{
		return array_flip($data);
	}
}