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
	 * @return array
	 */
	public function getSectionRoutes()
	{
		return [];
	}

	/**
	 * RouterManager constructor.
	 */
	public function __construct(RequestStack $request)
	{
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