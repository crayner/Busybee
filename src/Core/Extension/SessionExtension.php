<?php
namespace App\Core\Extension;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Twig\Extension\AbstractExtension;

class SessionExtension extends AbstractExtension
{
	/**
	 * @var Session
	 */
	private $session;

	/**
	 * ButtonExtension constructor.
	 *
	 * @param Session $session
	 */
	public function __construct(RequestStack $request)
	{
		if (! is_null($request->getCurrentRequest()) && $request->getCurrentRequest()->hasSession())
			$this->session = $request->getCurrentRequest()->getSession();
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'session_twig_extension';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFunctions()
	{
		return [
			new \Twig_SimpleFunction('hideSection', array($this, 'hideSection')),
		];
	}

	/**
	 * @param   string $value
	 *
	 * @return  int
	 */
	public function hideSection($route)
	{
		if (! $this->isSessionSet())
			return false;

		$hs = $this->session->get('hideSection');
		if (isset($hs[$route]))
			return $hs[$route];

		return false;
	}

	/**
	 * @return bool
	 */
	private function isSessionSet()
	{
		if (! $this->session instanceof Session)
			return false;
		return $this->session->isStarted();
	}
}