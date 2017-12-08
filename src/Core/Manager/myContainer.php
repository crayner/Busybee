<?php
namespace App\Core\Manager;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class myContainer implements ContainerInterface
{
	/**
	 * @var    Container
	 */
	private $container;

	/**
	 * Constuctor
	 */
	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
	}

	/**
	 * Gets a parameter.
	 *
	 * @param string $name The parameter name
	 *
	 * @return mixed The parameter value
	 *
	 * @throws ParameterNotFoundException if the parameter is not defined
	 */
	public function getParameter($name, $throw = true)
	{
		if (false === (strpos($name, '.')))
			return $this->loadParameter($name, $throw);

		$name  = explode('.', $name);

		if ($this->has($name[0]))
		{
			$value = $this->loadParameter($name[0], false);
			array_shift($name);
			while (!empty($name))
			{
				$key   = reset($name);
				$value = $value[$key];
				array_shift($name);
			}

			return $value;
		}
	}

	/**
	 * Load a parameter.
	 *
	 * The system always fails if the parameter is not set, so this allows the paramater get to step over the Parameter Not Found Error. <br>
	 * The error is still thrown by default.
	 *
	 * @param    string $name The parameter name
	 *
	 * @return    mixed  $value The parameter value
	 */
	private function loadParameter($name, $throw = true)
	{
		try
		{
			$value = $this->container->getParameterBag()->get($name);
		}
		catch (ParameterNotFoundException $e)
		{
			if ($throw)
				throw $e;
			$value = null;
		}

		return $value;
	}

	/**
	 * Sets a service.
	 *
	 * @param string $id      The service identifier
	 * @param object $service The service instance
	 */
	public function set($id, $service)
	{
		return $this->container->set($id, $service);
	}

	/**
	 * Gets a service.
	 *
	 * @param string $id              The service identifier
	 * @param int    $invalidBehavior The behavior when the service does not exist
	 *
	 * @return object The associated service
	 *
	 * @throws ServiceCircularReferenceException When a circular reference is detected
	 * @throws ServiceNotFoundException          When the service is not defined
	 *
	 * @see Reference
	 */
	public function get($id, $invalidBehavior = self::EXCEPTION_ON_INVALID_REFERENCE)
	{
		return $this->container->get($id, $invalidBehavior);
	}

	/**
	 * Returns true if the given service is defined.
	 *
	 * @param string $id The service identifier
	 *
	 * @return bool true if the service is defined, false otherwise
	 */
	public function has($id)
	{
		return $this->container->has($id);
	}

	/**
	 * Check for whether or not a service has been initialized.
	 *
	 * @param string $id
	 *
	 * @return bool true if the service has been initialized, false otherwise
	 */
	public function initialized($id)
	{
		return $this->container->initialized($id);
	}

	/**
	 * Checks if a parameter exists.
	 *
	 * @param string $name The parameter name
	 *
	 * @return bool The presence of parameter in container
	 */
	public function hasParameter($name)
	{
		return $this->container->hasParameter($name);
	}

	/**
	 * Sets a parameter.
	 *
	 * @param string $name  The parameter name
	 * @param mixed  $value The parameter value
	 */
	public function setParameter($name, $value)
	{
		return $this->container->setParameter($name, $value);
	}

	/**
	 * Gets the service container parameter bag.
	 *
	 * @return ParameterBagInterface A ParameterBagInterface instance
	 */
	public function getParameterBag()
	{
		return $this->container->getParameterBag();
	}

}
