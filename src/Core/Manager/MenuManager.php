<?php
namespace App\Core\Manager;

use Hillrange\Security\Util\PageManager;
use Doctrine\DBAL\Exception\ConnectionException;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Psr\Container\ContainerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Symfony\Component\Yaml\Yaml;

class MenuManager extends MenuManagerConstants
{
	/**
	 * @var object|\Symfony\Component\Security\Core\Authorization\AuthorizationChecker
	 */
	private $checker;

	/**
	 * @var PageManager
	 */
	private $pageManager;
	/**
	 * @var array
	 */
	private $pageRoles;

	/**
	 * @var array
	 */
	private $nodes;

	/**
	 * @var array
	 */
	private $nodeItems;

	/**
	 * @var RouterManager
	 */
	private $routerManager;

	/**
	 * @var ContainerInterface
	 */
	private $container;

	/**
	 * MenuManager constructor.
	 *
	 * @param PageManager                   $pageManager
	 * @param AuthorizationCheckerInterface $authChecker
	 * @param RouterManager                 $routerManager
	 */
	public function __construct(PageManager $pageManager, AuthorizationCheckerInterface $authChecker, RouterManager $routerManager, ContainerInterface $container)
	{
		$this->pageManager = $pageManager;
		$this->checker = $authChecker;
		$this->container = $container;

		$this->routerManager = $routerManager;

		$x = [];
		try
		{
			$x = $this->pageManager->getPageRepository()->findAll();
		} catch (TableNotFoundException $e) {
		} catch (ConnectionException $e) {
		}

		$this->pageRoles = [];
		foreach ($x as $page)
		{
			$this->pageRoles[$page->getRoute()] = $page->getRoles();
		}

		$this->nodes     = [];
		$this->nodeItems = [];

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getMenu()
	{
		if (!empty($this->nodes))
			return $this->nodes;

		$nodes = $this->getNodes();

		$nodes = $this->msort($nodes, 'order');
		foreach ($nodes as $q => $node)
		{
			$items = $this->getMenuItems($node['menu']);
			if (empty($items))
				unset($nodes[$q]);
		}

		$this->nodes = $nodes;

		return $nodes;
	}

	/**
	 * Array sort for multidimensional arrays
	 *
	 * @param        $array
	 * @param string $field
	 *
	 * @return mixed
	 */
	private function msort($array, $field = 'order')
	{
		usort($array, function ($a, $b) use ($field) {
			return $a[$field] <=> $b[$field];
		}
		);

		return $array;
	}

	/**
	 * @param $node
	 *
	 * @return mixed
	 */
	public function getMenuItems($node)
	{
		if (!empty($this->nodeItems[$node]))
			return $this->nodeItems[$node];
		$items  = $this->getItems();
		$result = [];
		foreach ($items as $w)
		{
			if ($w['node'] == $node && $this->itemCheck($w))
			{
				$w['parameters'] = ! empty($w['parameters']) ? $w['parameters'] : array();
				if (isset($w['route']))
					$w['role'] = $this->getRouteAccess($w['route'], empty($w['role']) ? null : $w['role']);
				if (empty($w['role']))
					unset($w['role']);
				$result[] = $w;
			}
		}
		$items = $this->msort($result, 'order');

		$this->nodeItems[$node] = $items;

		return $items;
	}


	private function itemCheck($node)
	{
		return $this->itemRoleCheck($node) && $this->showItem($node);
	}
	/**
	 * @param   array $node
	 *
	 * @return  bool
	 */
	private function itemRoleCheck($node)
	{
		if (null === $this->checker)
			return false;
		if (empty($node['role']) && empty($node['route']))
			return true;

		if (empty($node['role']) && empty($this->pageRoles[$node['route']]))
			return true;

		if (! empty($node['route']))
		{
			$this->pageRoles[$node['route']] = array_values(empty($this->pageRoles[$node['route']]) ? [] : $this->pageRoles[$node['route']]);

			if (empty($this->pageRoles[$node['route']]) || (count($this->pageRoles[$node['route']]) == 1 && is_null($this->pageRoles[$node['route']][0])))
				$this->pageRoles[$node['route']] = [];

			if (!empty($node['role']))
				$this->pageRoles[$node['route']] = array_merge($this->pageRoles[$node['route']], [$node['role']]);

			if (empty($this->pageRoles[$node['route']]))
				return true;

			foreach ($this->pageRoles[$node['route']] as $role)
			{
				try
				{
					if ($this->checker->isGranted($role))
						return true;
				}
				catch (AuthenticationCredentialsNotFoundException $e)
				{
					// Do Nothin!
				}
			}

			return false;
		}
		else
		{
			if (empty($node['role']))
				return true;

			$role = $node['role'];
			try
			{
				return $this->checker->isGranted($role);
			}
			catch (AuthenticationCredentialsNotFoundException $e)
			{
				return false;
			}
		}
	}

	/**
	 * get Route Access
	 *
	 * @param string $route
	 * @param string $role
	 *
	 * @return array
	 */
	private function getRouteAccess($route, $role)
	{
		$roles = [];
		$page  = $this->pageManager->findOneByRoute($route, $role);
		if (!empty($page))
			$roles = $page->getRoles();
		if (in_array(null, $roles))
			$roles = null;

		return $roles;
	}

	/**
	 * test Menu Item
	 *
	 * @param string $test
	 *
	 * @return bool
	 * @throws InvalidArgumentException
	 */
	public function testMenuItem($test)
	{
		$test['default1'] = isset($test['default1']) ? $test['default1'] : null;
		$test['default2'] = isset($test['default2']) ? $test['default2'] : null;
		$value1           = $this->manageValue($test['value1'], $test['default1']);
		$value2           = $this->manageValue($test['value2'], $test['default2']);

		$test['comparitor'] = empty($test['comparitor']) ? '=' : $test['comparitor'];
		switch ($test['comparitor'])
		{
			case '==':
				if ($value1 == $value2) return true;
				break;
			case '!=':
				if ($value1 != $value2) return true;
				break;
			case '<':
				if ($value1 < $value2) return true;
				break;
			default:
				throw new InvalidArgumentException('Do not know how to deal with ' . $test['comparitor'] . ' in ' . __FILE__);
		}

		return false;
	}

	/**
	 * @return    mixed
	 */
	private function manageValue($value, $default = null)
	{
		if (0 === strpos($value, 'setting.'))
			return $this->container->get('busybee_core_system.setting.setting_manager')->get(substr($value, 8), $default);

		if (0 === strpos($value, 'parameter.'))
		{
			$name = substr($value, 10);
			if (strpos($name, '.') === false)
				return $this->container->getParameter($name);
			$name  = explode('.', $name);
			$value = $this->container->getParameter($name[0]);
			array_shift($name);
			while (!empty($name))
			{
				$key   = reset($name);
				$value = $value[$key];
				array_shift($name);
			}

			return $value;
		}

		if (0 === strpos($value, 'test.'))
			return $this->container->get($value)->test();

		return $value;
	}

	/**
	 * @return    boolean
	 */
	public function menuRequired($menu)
	{
		$items = $this->getItems();
		foreach ($items as $item)
			if (intval($menu) == intval($item['node']))
				return true;

		return false;
	}

	/**
	 * @return array
	 */
	public function getSection()
	{
		$sections = $this->getSections();

		$routes = $this->getSectionRoutes($sections);

		$currentRoute = $this->routerManager->getCurrentRoute();

		$route = isset($routes[$currentRoute]) ? $routes[$currentRoute] : [];

		if (empty($route))
			return [];

		$sections = $sections[$route['section']];

		foreach ($sections as $q => $w)
		{
			if ($q !== 'hidden')
			{
				foreach ($w as $e => $r)
				{
					$sections[$q][$e]['linkClass'] = 'sectionLink';
					if ($r['route'] === $currentRoute)
						$sections[$q][$e]['linkClass'] .= ' currentLink';
					if (!empty($r['role']) && false === $this->checker->isGranted($r['role']))
					{
						unset($sections[$q][$e]);
					}
				}
				if (empty($sections[$q]))
					unset($sections[$q]);
			}
			else
			{
				unset($sections[$q]);
			}
		}

		return $sections;
	}

	/**
	 * @return array
	 */
	private function getNodes()
	{
		return Yaml::parse(str_replace("\t", "    ", MenuManagerConstants::NODES));
	}

	/**
	 * @return array
	 */
	private function getItems()
	{
		return Yaml::parse(str_replace("\t", "    ", MenuManagerConstants::ITEMS));
	}

	/**
	 * @return array
	 */
	private function getSections()
	{
		return Yaml::parse(str_replace("\t", "    ", MenuManagerConstants::SECTIONS));
	}

	/**
	 * @param $sections
	 *
	 * @return array
	 */
	private function getSectionRoutes($sections)
	{
		$routes = [];
		foreach ($sections as $name => $header)
		{
			if (!is_array($header))
				throw new \InvalidArgumentException('Section are not formatted correctly.');

			foreach ($header as $headName => $data)
				if ($headName !== 'hidden')
				{
					foreach ($data as $x)
					{
						$key                     = $x['route'];
						$routes[$key]['section'] = $name;
						$routes[$key]['header']  = $headName;
					}
				}
				else
				{
					foreach ($data as $key)
					{
						$routes[$key]['section'] = $name;
						$routes[$key]['header']  = $headName;
					}
				}

		}
		return $routes;
	}

	private function showItem($node)
	{
		if (empty($node['showTest']))
			return true;

		$className = $node['showTest'];

		if (! class_exists($className, true))
			return false;
		$class = $this->container->get($className);

		return $class->showItem();
	}
}