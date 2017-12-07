<?php
namespace App\Controller;

use App\Install\Form\StartInstallType;
use App\Install\Manager\InstallManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class InstallController extends BusybeeController
{
	/**
	 * @param Request $request
	 * @Route("/install/build/", name="install_build")
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function installBuild(Request $request, InstallManager $installer)
	{
		$installer->signin = null;

		$params = $installer->getParameters();
		$sql    = $installer->getSQLParameters($params);

		$form = $this->createForm(StartInstallType::class, null, ['data' => $sql]);

		$sql = $installer->handleDataBaseRequest($form, $request);

		dump($sql);

		$testDatabase = false;
		if (! empty($sql['name']) && ! empty($sql['user']) && ! empty($sql['password']))
			$testDatabase = true;

		if (($form->isSubmitted() && $form->isValid()) || $testDatabase)
		{

			if (!$installer->testConnected($sql))
			{
				return $this->render('BusybeeInstallBundle:Install:start.html.twig',
					[
						'config' => $installer,
						'form'   => $form->createView(),
					]
				);

			}

			if (!$installer->hasDatabase())
			{
				return $this->render('BusybeeInstallBundle:Install:start.html.twig',
					[
						'config' => $installer,
						'form'   => $form->createView(),
					]
				);

			}
			$installer->getSql()->displayForm = false;

			if ($installer->saveDatabase)
			{
				$this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('install.database.save.success', [], 'BusybeeInstallBundle'));
			}
			elseif($installer->getSql()->connected)
			{
				$this->get('session')->getFlashBag()->add('info', $this->get('translator')->trans('install.database.save.already', [], 'BusybeeInstallBundle'));
				$installer->getSql()->displayForm = true;
			}
			else
			{
				$this->get('session')->getFlashBag()->add('danger', $this->get('translator')->trans('install.database.save.failed', [], 'BusybeeInstallBundle'));
				$installer->getSql()->connected = false;
				$installer->getSql()->error     = $this->get('translator')->trans('install.database.save.failed', [], 'BusybeeInstallBundle');
			}
		}
		else
		{
			$installer->getSql()->connected = false;
			$installer->getSql()->error     = $this->get('translator')->trans('install.database.not.tested', [], 'BusybeeInstallBundle');
		}

		return $this->render('Install/start.html.twig',
			[
				'config'          => $installer,
				'form'            => $form->createView(),
			]
		);
	}
}