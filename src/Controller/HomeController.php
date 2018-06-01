<?php
namespace App\Controller;

use App\Core\Manager\MessageManager;
use App\Entity\Setting;
use App\Install\Manager\VersionManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Yaml\Yaml;
use Symfony\Requirements\SymfonyRequirements;

class HomeController extends Controller
{
	/**
	 * @Route("/", name="home")
	 */
	public function home(Request $request, MessageManager $messages)
	{
		if ($request->getSession()->has(Security::AUTHENTICATION_ERROR))
		{
			$messages->setDomain('security');
			$error = $request->getSession()->get(Security::AUTHENTICATION_ERROR);
			$request->getSession()->remove(Security::AUTHENTICATION_ERROR);

			if ($error->getCode() == 773)
				$messages->add('warning', 'security.login.ip.blocked', ['%{ip}' => $request->server->get('REMOTE_ADDR')]);
			elseif ($error->getCode() == 774)
				$messages->add('warning', 'security.login.user.expired');
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

		$SymfonyRequirements = new SymfonyRequirements($versionManager->getSettingManager()->getParameter('kernel.root_dir'));

		return $this->render('Acknowledgement/acknowledgement.html.twig',
			[
				'versions'      => $versions,
				'majorProblems' => $SymfonyRequirements->getFailedRequirements(),
				'minorProblems' => $SymfonyRequirements->getFailedRecommendations(),
				'manager'       => $versionManager,
			]
		);
	}
	/**
	 * @param string $file
	 * @param string $role
	 * @Route("/download/file/{file}/{role}/", name="download_file")
	 * @return Response
	 * @throws \Exception
	 */
	public function downloadFileAction($file, $role = 'ROLE_SYSTEM_ADMIN')
	{
		$this->denyAccessUnlessGranted($role);

		if (! empty($file) && file_exists(base64_decode($file)))
		{
			$content = file_get_contents(base64_decode($file));
			$file    = new File(base64_decode($file));
			$headers = array(
				'Content-type'        => $file->getMimeType(),
				'Content-Disposition' => 'attachment; filename=' . basename($file->getPathname()),
				'Content-Length'      => $file->getSize(),
			);

			return new Response($content, 200, $headers);
		}

		throw new \Exception('The file is not available to download. ' . base64_decode($file));
	}
    /**
     * @Route("/locked/", name="_locked")
     */
    public function locked(VersionManager $versionManager)
    {
        return $this->render('Default/locked.html.twig',
            [
                'version' => $versionManager,
            ]
        );
    }


}