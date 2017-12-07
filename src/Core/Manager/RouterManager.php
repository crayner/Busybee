<?php
namespace App\Core\Manager;


class RouterManager
{
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
	public function __construct(Request $request)
	{
		dump($request);
		return $this;
	}
}