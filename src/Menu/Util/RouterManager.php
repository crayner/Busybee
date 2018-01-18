<?php
namespace App\Menu\Util;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class RouterManager
{
	/**
	 * @var string
	 */
	private $currentRoute;

	/**
	 * RouterManager constructor.
	 */
	public function __construct(RequestStack $request)
	{
		if (! is_null($request->getCurrentRequest()))
			$this->setCurrentRoute($request->getCurrentRequest());

		return $this;
	}

	/**
	 * @return string
	 */
	public function getCurrentRoute(): string
	{
		return $this->currentRoute;
	}

	public function setCurrentRoute(Request $request)
	{
		if (! empty($this->currentRoute))
			return $this;

		$this->currentRoute = $request->get('_route');

		return $this;
	}
}