<?php
namespace App\Controller;

use App\Install\Manager\VersionManager;
use App\Install\Form\StartInstallType;
use App\Install\Manager\InstallManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class InstallController extends BusybeeController
{
	/**
	 * @param Request $request
	 * @Route("/install/build/", name="install_build")
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function installBuild(Request $request, InstallManager $installer, VersionManager $versionManager)
	{
		$installer->signin = null;

		$sql    = $installer->getSQLParameters();

		$form = $this->createForm(StartInstallType::class, $sql);

		$sql = $installer->handleDataBaseRequest($form, $request);

		$session = $request->getSession();
		if (is_null($session))
			$session = new Session();

		if (! $session->isStarted()) {
			$session->setName('busybee');
			$session->start();
		}

		$testDatabase = false;
		if (! empty($sql['name']) && ! empty($sql['user']) && ! empty($sql['password']))
			$testDatabase = true;

		if (($form->isSubmitted() && $form->isValid()) || $testDatabase)
		{
			$cf = $this->get('doctrine');

			if (!$installer->testConnected($sql, $cf))
			{
				return $this->render('Install/start.html.twig',
					[
						'config' => $installer,
						'form'   => $form->createView(),
					]
				);

			}

			if (!$installer->hasDatabase())
			{
				return $this->render('Install/start.html.twig',
					[
						'config' => $installer,
						'form'   => $form->createView(),
					]
				);

			}
			$installer->getSql()->displayForm = false;

			if ($installer->saveDatabase)
			{
				$session->getFlashBag()->add('success', $this->get('translator')->trans('install.database.save.success', [], 'Install'));
			}
			elseif($installer->getSql()->connected)
			{
				$session->getFlashBag()->add('info', $this->get('translator')->trans('install.database.save.already', [], 'Install'));
				$installer->getSql()->displayForm = true;
			}
			else
			{
				$session->getFlashBag()->add('danger', $this->get('translator')->trans('install.database.save.failed', [], 'Install'));
				$installer->getSql()->connected = false;
				$installer->getSql()->error     = $this->get('translator')->trans('install.database.save.failed', [], 'Install');
			}
		}
		else
		{
			$installer->getSql()->connected = false;
			$installer->getSql()->error     = $this->get('translator')->trans('install.database.not.tested', [], 'Install');
		}

		return $this->render('Install/start.html.twig',
			[
				'config'          => $installer,
				'form'            => $form->createView(),
				'version_manager' => $versionManager,
			]
		);
	}

	/**
	 * @param Request $request
	 * @Route("/install/mailer/", name="install_check_mailer_parameters")
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function mailerInstall(InstallManager $installer, Request $request)
	{
		$w = $installer->getConfig();

		//turn spooler off
		$swift              = $w['swiftmailer'];
		$swift['transport'] = "%mailer_transport%";
		$swift['host']      = "%mailer_host%";
		$swift['username']  = "%mailer_user%";
		$swift['password']  = "%mailer_password%";
		$w['swiftmailer']   = $swift;
		$installer->saveConfig($w);

		$installer->getMailerParameters();
		$installer->mailer->canDeliver = false;

		$form = $this->createForm(MailerType::class);

		$installer->handleMailerRequest($form, $request);

		$installer->mailer->canDeliver = false;
		if ($installer->mailer->mailer_transport != '')
		{
			$message                    = \Swift_Message::newInstance()
				->setSubject('Test Email')
				->setFrom($installer->mailer->mailer_sender_address, $installer->mailer->mailer_sender_name)
				->setTo($installer->mailer->mailer_sender_address, $installer->mailer->mailer_sender_name)
				->setBody(
					$this->renderView(
						'BusybeeInstallBundle:Emails:test.html.twig', []
					),
					'text/html'
				)/*
				 * If you also want to include a plaintext version of the message
				->addPart(
					$this->renderView(
						'Emails/registration.txt.twig', []
					),
					'text/plain'
				)
				*/
			;
			$installer->mailer->canDeliver = true;
			try
			{
				$mailer = $this->get('mailer')->send($message);
			}
			catch (\Swift_TransportException $e)
			{
				$this->get('session')->getFlashBag()->add('error', $e->getMessage());
				$installer->mailer->canDeliver = false;
			}
			catch (\Swift_RfcComplianceException $e)
			{
				$this->get('session')->getFlashBag()->add('error', $e->getMessage());
				$installer->mailer->canDeliver = false;
			}
		}

		if ($form->isSubmitted())
		{
			if ($installer->saveMailer)
				$this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('mailer.save.success', [], 'BusybeeInstallBundle'));
			else
			{
				$this->get('session')->getFlashBag()->add('danger', $this->get('translator')->trans('mailer.save.failed', [], 'BusybeeInstallBundle'));
				$installer->mailer->canDeliver = false;
			}
		} elseif ($installer->mailer->canDeliver)
		{
			$this->get('session')->getFlashBag()->add('info', $this->get('translator')->trans('mailer.save.already', [], 'BusybeeInstallBundle'));
		}

		return $this->render('BusybeeInstallBundle:Install:checkMailer.html.twig',
			[
				'config' => $installer,
				'form'   => $form->createView(),
			]
		);
	}

}