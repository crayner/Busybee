<?php
namespace App\Controller;

use App\Core\Manager\MessageManager;
use App\Install\Form\MailerType;
use App\Install\Form\MiscellaneousType;
use App\Install\Manager\SystemBuildManager;
use App\Install\Manager\VersionManager;
use App\Install\Form\StartInstallType;
use App\Install\Manager\InstallManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class InstallController extends Controller
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

		$sql = $installer->getSQLParameters();

		$form = $this->createForm(StartInstallType::class, $sql);

		$installer->handleDataBaseRequest($form, $request);

		$session = $request->getSession();
		if (is_null($session))
			$session = new Session();

		if (! $session->isStarted()) {
			$session->setName('busybee');
			$session->start();
		}

		$testDatabase = false;
		if (! empty($sql->getName()) && ! empty($sql->getUser()) && ! empty($sql->getPass()))
			$testDatabase = true;


		$messages = new MessageManager('Install');

		if (($form->isSubmitted() && $form->isValid()) || $testDatabase)
		{
			if (! $installer->testConnected())
			{
				dump($form->isSubmitted());
				return $this->render('Install/start.html.twig',
					[
						'config' => $installer,
						'form'   => $form->createView(),
					]
				);

			}

			if (! $installer->hasDatabase())
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
				$messages->add('success', 'install.database.save.success');
			}
			elseif($installer->getSql()->isConnected())
			{
				$messages->add('info', 'install.database.save.already');
				$installer->getSql()->displayForm = true;
			}
			else
			{
				$messages->add('danger', 'install.database.save.failed');
				$installer->getSql()->setConnected(false);
				$installer->getSql()->error     = $this->get('translator')->trans('install.database.save.failed', [], 'Install');
			}
		}
		else
		{
			$installer->getSql()->setConnected(false);
			$installer->getSql()->error     = $this->get('translator')->trans('install.database.not.tested', [], 'Install');
		}

		if ($form->isSubmitted() && ! $form->isValid())
			$messages->add('danger', 'form.notvalid', [], 'FormTheme');

		return $this->render('Install/start.html.twig',
			[
				'config'          => $installer,
				'form'            => $form->createView(),
				'version_manager' => $versionManager,
				'messages'        => $messages,
			]
		);
	}

	/**
	 * @param Request $request
	 * @Route("/install/mailer/", name="install_check_mailer_parameters")
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function mailerInstall(InstallManager $installer, Request $request, MessageManager $messages, \Swift_Mailer $swiftMailer)
	{
		$mailer = $installer->getMailerConfig();

		//turn spooler off
		$swift              = $mailer->getSwiftmailer();
		$swift['url']       = '%env(MAILER_URL)%';
		$swift['transport'] = "%mailer_transport%";
		$swift['host']      = "%mailer_host%";
		$swift['username']  = "%mailer_user%";
		$swift['password']  = "%mailer_password%";
		$swift['spool']     = "%mailer_spool%";

		$installer->saveMailerConfig($mailer, false);

		$installer->getMailer()->setCanDeliver(false);

		$form = $this->createForm(MailerType::class, $mailer);

		$installer->handleMailerRequest($form, $request);

		$installer->getMailer()->setCanDeliver(false);

		$messages->setDomain('Install');

				if ($installer->getMailer()->getTransport() != '')
		{
			$email= (new \Swift_Message($this->renderView('Emails/test_header.html.twig', ['direct' => true])))
				->setFrom($installer->getMailer()->getSenderAddress(), $installer->getMailer()->getSenderName())
				->setTo($installer->getMailer()->getSenderAddress(), $installer->getMailer()->getSenderName())
				->setBody(
					$this->renderView(
						'Emails/test.html.twig'
					),
					'text/html'
				)
			;
			$installer->getMailer()->setCanDeliver(true);

			try
			{
				$swiftMailer->send($email);
			}
			catch (\Swift_TransportException $e)
			{
				$messages->add('error', $e->getMessage());
				$installer->getMailer()->setCanDeliver(false);
			}
			catch (\Swift_RfcComplianceException $e)
			{
				$messages->add('error', $e->getMessage());
				$installer->getMailer()->setCanDeliver(false);
			}
			if ($installer->getMailer()->isCanDeliver())
			{
				$spool = $swiftMailer->getTransport()->getSpool();
				$transport = new \Swift_SmtpTransport($installer->getMailer()->getHost(), $installer->getMailer()->getPort(), $installer->getMailer()->getEncryption());
				$transport
					->setUsername($installer->getMailer()->getUser())
					->setPassword($installer->getMailer()->getPassword())
				;
				$ok = true;

				try
				{
					$spool->flushQueue($transport);
				} catch (\Exception $e) {
					$messages->add('warning', 'mailer.delivered.failed', ['%{address}' => $installer->getMailer()->getSenderAddress(), '%{message}' => $e->getMessage()]);
					$ok = false;
				}
				if ($ok)
				{
					$messages->add('success', 'mailer.delivered.success', ['%{email}' => $installer->getMailer()->getSenderAddress()]);

					$email = (new \Swift_Message($this->renderView('Emails/test_header.html.twig', ['direct' => false])))
						->setFrom($installer->getMailer()->getSenderAddress(), $installer->getMailer()->getSenderName())
						->setTo($installer->getMailer()->getSenderAddress(), $installer->getMailer()->getSenderName())
						->setBody(
							$this->renderView(
								'Emails/test.html.twig'
							),
							'text/html'
						)
					;
					$swiftMailer->send($email);
				}
			}
		}

		if ($form->isSubmitted())
		{
			if ($installer->isMailerSaved())
			{
				$messages->add('success', 'mailer.save.success');
				if (! $installer->getMailer()->isCanDeliver())
					$messages->add('warning', 'mailer.delivered.warning');

			}
			else
			{
				$messages->add('danger', 'mailer.save.failed');
				$installer->getMailer()->setCanDeliver(false);
			}
		}
		elseif ($installer->getMailer()->isCanDeliver())
		{
			$messages->add('info', 'mailer.save.already');
		}
		if ($form->isSubmitted() && ! $form->isValid())
			$messages->add('danger', 'form.notvalid', [], 'FormTheme');

		return $this->render('Install/checkMailer.html.twig',
			[
				'config' => $installer,
				'form'   => $form->createView(),
				'messages' => $messages,
			]
		);
	}


	/**
	 * @param Request $request
	 * @Route("/install/miscellaneous/", name="install_misc_check")
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function miscellaneousInstall(Request $request, InstallManager $installer)
	{
		$installer->setProceed(false);

		$misc = $installer->getMiscellaneousConfig();

		$form = $this->createForm(MiscellaneousType::class, $misc);

		$installer->handleMiscellaneousRequest($form, $request);

		$messages = new MessageManager('Install');

		if ($form->isSubmitted() && ! $form->isValid())
			$messages->add('danger', 'form.notvalid', [], 'FormTheme');
		if ($form->isSubmitted() && $form->isValid())
			$messages->add('success', 'form.valid', [], 'FormTheme');


			return $this->render('Install/misc.html.twig',
			[
				'config' => $installer,
				'form'   => $form->createView(),
				'messages' => $messages,
			]
		);
	}

	/**
	 * @param Request $request
	 * @Route("/install/database/", name="install_database")
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function installDatabase(SystemBuildManager $systemBuildManager)
	{
		return $this->render('Install/database.html.twig',
			[
				'manager' => $systemBuildManager,
				'projectDir' => $this->getParameter('kernel.project_dir'),
			]
		);
	}

	/**
	 * @param Request $request
	 * @Route("/install/system/settings/", name="install_system_settings")
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function installSytemSettings(SystemBuildManager $systemBuildManager)
	{
		return $this->render('Install/system_settings.html.twig',
			[
				'manager' => $systemBuildManager,
			]
		);
	}
}