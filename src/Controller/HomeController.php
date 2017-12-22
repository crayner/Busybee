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
		return $this->render('base.html.twig');
	}
}