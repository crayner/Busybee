<?php
namespace App\Controller;

use App\Core\Manager\MessageManager;
use App\Entity\Person;
use App\Install\Form\MailerType;
use App\Install\Form\MailerTypeTrait;
use App\Install\Form\MiscellaneousType;
use App\Install\Form\UserType;
use App\Install\Manager\SystemBuildManager;
use App\Install\Manager\VersionManager;
use App\Install\Form\StartInstallType;
use App\Install\Manager\InstallManager;
use App\Install\Organism\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;

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
	public function mailerInstall(InstallManager $installer, Request $request, MessageManager $messages, \Swift_Mailer $swiftMailer, \Twig_Environment $twig)
	{
        $installer = $installer->getMailerManager();
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

		$installer->testDelivery($twig, $messages, $swiftMailer);

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
	public function installDatabase(SystemBuildManager $systemBuildManager, Request $request)
	{
	    if ($request->getSession()->isStarted())
	        $request->getSession()->start();

        $request->getSession()->invalidate();

        $systemBuildManager->setAction(true);

        $installed = $systemBuildManager->buildDatabase();

        $systemBuildManager->buildSystemSettings();

		return $this->render('Install/database.html.twig',
			[
				'installed' => $installed,
				'manager' => $systemBuildManager,
			]
		);
	}

	/**
	 * @param Request $request
	 * @Route("/install/user/", name="install_user")
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function installUser(Request $request, SystemBuildManager $systemBuildManager)
    {
        if (version_compare($systemBuildManager->getSystemVersion(), '0.0.00', '='))
        {
            $systemBuildManager->writeSystemUser();
            $systemBuildManager->setAction(true);
            $systemBuildManager->getSettingManager()->setInstallMode(true);
            $systemBuildManager->buildSystemSettings();
            return $this->render('Install/system_settings.html.twig',
                [
                    'manager' => $systemBuildManager,
                ]
            );
        }

		$user = new User();

		$user->setPasswordNumbers($systemBuildManager->getSettingManager()->getParameter('password_numbers'));
		$user->setPasswordMixedCase($systemBuildManager->getSettingManager()->getParameter('password_mixed_case'));
		$user->setPasswordSpecials($systemBuildManager->getSettingManager()->getParameter('password_specials'));
		$user->setPasswordMinLength($systemBuildManager->getSettingManager()->getParameter('password_min_length'));

		$form = $this->createForm(UserType::class, $user);

		$systemBuildManager->handleUserParameters($request, $form);

		if ($form->isSubmitted() && $form->isValid())
		{
            $data = $request->get('install_user');

			$systemBuildManager->writeSystemUser($data);

            $em = $this->get('doctrine')->getManager();

            $user1 = $em->getRepository(\Hillrange\Security\Entity\User::class)->find(1);

            $user1->setCredentialsExpireAt(null);
            $user1->setCredentialsExpired(false);

            $person = new Person();
            $person->setHonorific($data['title']);
            $person->setUser($user1);
            $person->setEmail($data['_email']);
            $person->setSurname($data['surname']);
            $person->setFirstName($data['firstName']);
            $person->setPreferredName($data['firstName']);
            $person->setOfficialName($data['firstName'] . ' ' . $data['surname']);

            $settingManager = $systemBuildManager->getSettingManager();

            $settingManager->setInstallMode(true);

            $settingManager->set('currency', $data['currency']);
            $orgName = [];
            $orgName['long'] = $data['orgName'];
            $orgName['short'] = $data['orgNameShort'];
            $settingManager->set('org.name', $orgName);

            $google = [];
            $google['o_auth'] = $data['googleOAuth'];
            $google['client_id'] = $data['googleClientId'];
            $google['client_secret'] = $data['googleClientSecret'];
            $settingManager->set('google', $google);

            $params = Yaml::parse(file_get_contents($settingManager->getParameter('kernel.project_dir').'/config/packages/busybee.yaml'));

            $params['parameters']['country'] = $data['country'];
            $params['parameters']['timezone'] = $data['timezone'];

            file_put_contents($settingManager->getParameter('kernel.project_dir').'/config/packages/busybee.yaml', Yaml::dump($params));

            $person->setIdentifier('');

            $em->persist($user1);
            $em->persist($person);
            $em->flush();

            $request->getSession()->clear();

            return $this->redirectToRoute('home');

		}


		return $this->render('Install/user.html.twig',
			[
				'manager' => $systemBuildManager,
				'projectDir' => $systemBuildManager->getSettingManager()->getParameter('kernel.project_dir'),
				'form' => $form->createView(),
			]
		);
	}
}