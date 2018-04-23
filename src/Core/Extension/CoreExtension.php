<?php
namespace App\Core\Extension;

use App\Core\Manager\TwigManager;
use App\Core\Util\ScriptManager;
use Twig\Extension\AbstractExtension;

class CoreExtension extends AbstractExtension
{
    /**
     * @var TwigManager
     */
	private $twigManager;

	private $scriptManager;

    /**
     * CoreExtension constructor.
     * @param TwigManager $twigManager
     */
    public function __construct(TwigManager $twigManager, ScriptManager $scriptManager)
	{
		$this->twigManager      = $twigManager;
        $this->scriptManager    = $scriptManager;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFunctions()
	{
		return array(
            new \Twig_SimpleFunction('callManagerMethod', array($this->twigManager, 'callManagerMethod')),
            new \Twig_SimpleFunction('addScript', array($this->scriptManager, 'addScript')),
            new \Twig_SimpleFunction('getScripts', array($this->scriptManager, 'getScripts')),
		);
	}

    /**
     * @return string
     */
    public function getName()
	{
		return 'core_extension';
	}
}