<?php
namespace App\Controller;

use App\Core\Manager\StatusManager;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends BusybeeController
{
	/**
	 * @Route("/", name="home")
	 */
	public function home(SessionInterface $session = null)
	{
		$sm = new StatusManager();

		if (! $sm->isInstalled($session))
			return $this->redirectToRoute('install_build');

		return $this->render('base.html.twig');
	}
}