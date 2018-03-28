<?php
namespace App\Core\Extension;

use App\Core\Manager\TwigManager;
use Twig\Extension\AbstractExtension;

class CoreExtension extends AbstractExtension
{
    /**
     * @var TwigManager
     */
	private $twigManager;

    /**
     * CoreExtension constructor.
     * @param TwigManager $twigManager
     */
    public function __construct(TwigManager $twigManager)
	{
		$this->twigManager       = $twigManager;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFunctions()
	{
		return array(
			new \Twig_SimpleFunction('callManagerMethod', array($this->twigManager, 'callManagerMethod')),
		);
	}

    /**
     * @return string
     */
    public function getName()
	{
		return 'twig_manager_extension';
	}
}