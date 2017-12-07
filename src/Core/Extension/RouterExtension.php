<?php
namespace App\Core\Extension;

use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;

class RouterExtension extends AbstractExtension
{

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'router_twig_extension';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFunctions()
	{
		return array(
			new \Twig_SimpleFunction('routeExists', array($this, 'routeExists')),
		);
	}

	/**
	 * @param   string $value
	 *
	 * @return  bool
	 */
	public function routeExists($value): bool
	{
		return null !== $this->router->getRouteCollection()->get($value);
	}

	/**
	 * @var Router
	 */
	private $router;

	/**
	 * RouterExtension constructor.
	 *
	 * @param Router $router
	 */
	public function __construct(RouterInterface $router)
	{
		$this->router = $router;
	}
}