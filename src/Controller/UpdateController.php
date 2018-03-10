<?php
namespace App\Controller;

use App\Core\Form\UpdateType;
use App\Core\Manager\MenuUpdateTest;
use App\Install\Manager\SystemBuildManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UpdateController extends Controller
{
	/**
	 * @param Request $request
	 * @Route("/update/system/settings/", name="update_system_settings")
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function updateSystemSettings(SystemBuildManager $systemBuildManager, Request $request)
	{
		if (! $this->isGranted('ROLE_SYSTEM_ADMIN'))
		    return $this->redirectToRoute('_locked');

	    $form = $this->createForm(UpdateType::class);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
			$systemBuildManager->setAction(true);

		$systemBuildManager->buildDatabase();

		$systemBuildManager->buildSystemSettings();


		return $this->render('Update/system_settings.html.twig',
			[
				'manager' => $systemBuildManager,
				'form' => $form->createView(),
			]
		);
	}
}