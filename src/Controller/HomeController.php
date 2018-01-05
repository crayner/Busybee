<?php
namespace App\Controller;

use App\Core\Manager\MessageManager;
use App\Install\Manager\VersionManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Requirements\SymfonyRequirements;

class HomeController extends Controller
{
	/**
	 * @Route("/", name="home")
	 */
	public function home(Request $request, MessageManager $messages)
	{

		if ($request->getSession() && $request->getSession()->has(Security::AUTHENTICATION_ERROR))
		{
			$messages->setDomain('security');
			$error = $request->getSession()->get(Security::AUTHENTICATION_ERROR);
			$request->getSession()->remove(Security::AUTHENTICATION_ERROR);

			if ($error->getCode() == 773)
				$messages->add('warning', 'security.login.ip.blocked', ['%{ip}' => $request->server->get('REMOTE_ADDR')]);
			else
				$messages->add('warning', $error->getMessage());
		}
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
				'manager'       => $versionManager,
			]
		);
	}
}