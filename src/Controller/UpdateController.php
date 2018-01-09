<?php
namespace App\Controller;

use App\Install\Manager\SystemBuildManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UpdateController extends Controller
{

	/**
	 * @param Request $request
	 * @Route("/update/system/settings/", name="update_system_settings")
	 * @IsGranted("ROLE_SYSTEM_ADMIN")
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function updateSystemSettings(SystemBuildManager $systemBuildManager, TokenStorageInterface $tokenStorage, Request $request)
	{
		$listener = $this->get('Hillrange\Security\Listener\UserTrackListener');

		$listener->injectTokenStorage($tokenStorage, $request);

		$systemBuildManager->buildDatabase();

		$systemBuildManager->buildSystemSettings();
		return $this->render('Update/system_settings.html.twig',
			[
				'manager' => $systemBuildManager,
			]
		);
	}
}