<?php
namespace App\Core\Extension;

use App\Core\Manager\BundleManager;
use Twig\Extension\AbstractExtension;

class BundleExtension extends AbstractExtension
{
	/**
	 * @var BundleManager
	 */
	private $manager;

	/**
	 * ButtonExtension constructor.
	 *
	 * @param BundleManager $manager
	 */
	public function __construct(BundleManager $manager)
	{
		$this->manager = $manager;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'bundle_manager_extension';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFunctions()
	{
		return array(
			new \Twig_SimpleFunction('sectionMenuTest', array($this->manager, 'sectionMenuTest')),
		);
	}
}