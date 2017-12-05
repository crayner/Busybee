<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends BusybeeController
{

	/**
	 * @Route("/", name="home")
	 */
	public function home()
	{
		return $this->render('base.html.twig');
	}
}