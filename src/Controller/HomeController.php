<?php
namespace App\Controller;

use App\Core\Manager\StatusManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends Controller
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