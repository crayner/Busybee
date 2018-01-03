<?php
namespace App\Controller;

use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * Controller is a simple implementation of a Controller.
 *
 * It provides methods to common features needed in controllers.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Craig Rayner <craig@craigrayner.com>
 */
abstract class Controller extends \Symfony\Bundle\FrameworkBundle\Controller\Controller
{
	/**
	 * Gets a container configuration parameter by its name.
	 *
	 * modifed by Craig Rayner to allow default values in code.
	 *
	 * @return mixed
	 *
	 * @final since version 3.4
	 */
	protected function getParameter(string $name, $default = null)
	{
		if (is_null($default))
			return $this->container->getParameter($name);

		$x = null;
		try{
			$x = $this->container->getParameter($name);
		} catch (InvalidArgumentException $e) {
			return $default;
		}

		return $x;
	}
}
