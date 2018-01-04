<?php
namespace App\Controller;

use App\Install\Manager\VersionManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Requirements\SymfonyRequirements;

class HomeController extends Controller
{
	/**
	 * @Route("/", name="home")
	 */
	public function home(Request $request)
	{
		return $this->render('home.html.twig');
	}

	/**
	 * @Route("/template/", name="home_template")
	 */
	public function template()
	{
		return $this->render('Default/template.html.twig');
	}

	/**
	 * @Route("/acknowledgement/", name="acknowledgement")
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function acknowledgement(VersionManager $versionManager)
	{
		$versions = $versionManager->getVersion();

		$SymfonyRequirements = new SymfonyRequirements($this->getParameter('kernel.root_dir'));

		return $this->render('Acknowledgement/acknowledgement.html.twig',
			[
				'versions'      => $versions,
				'majorProblems' => $SymfonyRequirements->getFailedRequirements(),
				'minorProblems' => $SymfonyRequirements->getFailedRecommendations(),
			]
		);
	}
}