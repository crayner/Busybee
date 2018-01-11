<?php
namespace App\Core\Manager;

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
			$this->currentRoute = $request->getCurrentRequest()->get('_route');

		return $this;
	}

	/**
	 * @return string
	 */
	public function getCurrentRoute(): string
	{
		return $this->currentRoute;
	}
}