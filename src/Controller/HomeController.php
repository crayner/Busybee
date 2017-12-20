<?php
namespace App\Controller;

use App\Core\Manager\StatusManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends Controller
{
	/**
	 * @Route("/", name="home")
	 */
	public function home(Request $request, StatusManager $statusManager)
	{
		if (! $statusManager->isInstalled($request->getSession()))
			return $this->redirectToRoute('install_build');

		return $this->render('base.html.twig');
	}
}