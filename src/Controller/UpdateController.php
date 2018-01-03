<?php
namespace App\Controller;

use App\Install\Manager\SystemBuildManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UpdateController extends Controller
{

	/**
	 * @param Request $request
	 * @Route("/update/system/settings/", name="update_system_settings")
	 * @IsGranted("ROLE_SYSTEM_ADMIN")
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function updateSystemSettings(SystemBuildManager $systemBuildManager)
	{
		$systemBuildManager->buildDatabase();

		$systemBuildManager->buildSystemSettings();
		return $this->render('Update/system_settings.html.twig',
			[
				'manager' => $systemBuildManager,
			]
		);
	}
}