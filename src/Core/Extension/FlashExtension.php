<?php
namespace App\Core\Extension;

use Twig\Extension\AbstractExtension;

class FlashExtension extends AbstractExtension
{
	/**
	 * @var array
	 */
	private $flashMessage = [];

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'flash_extension';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFunctions()
	{
		return [
			new \Twig_SimpleFunction('showFlash', array($this, 'showFlash')),
		];
	}

	/**
	 * @param   string $value
	 *
	 * @return  bool
	 */
	public function showFlash($value): bool
	{
		if (in_array($value, $this->flashMessage))
			return false;

		$this->flashMessage[] = $value;

		return true;
	}
}