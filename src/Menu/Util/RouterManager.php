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
	 * @var RequestStack
	 */
	private $request;
	/**
	 * RouterManager constructor.
	 */
	public function __construct(RequestStack $request)
	{
		if (! is_null($request->getCurrentRequest()))
			$this->setCurrentRoute($request->getCurrentRequest());

		$this->request = $request;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getCurrentRoute(): ?string
	{
		$this->setCurrentRoute();

		return $this->currentRoute;
	}

	public function setCurrentRoute(Request $request = null)
	{
		if (! empty($this->currentRoute))
			return $this;

		if (is_null($request))
			$request = $this->request->getCurrentRequest();

		if ($request instanceof Request)
			$this->currentRoute = $request->get('_route');

		return $this;
	}
}