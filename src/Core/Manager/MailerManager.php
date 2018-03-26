<?php
namespace App\Core\Manager;

use App\Install\Organism\Mailer;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Yaml;

class MailerManager
{
    /**
     * @var null|string
     */
    private $projectDir;

    /**
     * MailerManager constructor.
     * @param null|string $projectDir
     */
    public function __construct(?string $projectDir)
    {
        $this->projectDir = $projectDir;
    }

    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * @var bool
     */
    private $mailerSaved = false;

    /**
     * Get Mailer Config
     *
     * @return array
     */
    public function getMailerConfig(): Mailer
    {
        $params = Yaml::parse(file_get_contents($this->projectDir . '/config/packages/swiftmailer.yaml'));

        $this->mailer = new Mailer($params);

        return $this->mailer;
    }

    /**
     * Save Mailer Config
     *
     * @param      $mailer
     * @param bool $writeUrl
     *
     * @return bool
     */
    public function saveMailerConfig($mailer, $writeUrl = true)
    {
        $this->mailer = $mailer;

        $this->mailerSaved = file_put_contents($this->projectDir . '/config/packages/swiftmailer.yaml', Yaml::dump($this->mailer->dumpMailerSettings()));

        if ($this->mailerSaved && $writeUrl) {
            $env = file($this->projectDir . '/.env');
            foreach ($env as $q => $w) {
                if (strpos($w, 'MAILER_URL=') === 0)
                    $env[$q] = $this->mailer->getUrl();
                $env[$q] = trim($env[$q]);
            }
            $env = implode($env, "\r\n");

            $this->mailerSaved = file_put_contents($this->projectDir . '/.env', $env);
        }

        return $this->mailerSaved;
    }

    /**
     * @return Mailer|null
     */
    public function getMailer(): ?Mailer
    {
        if (!$this->mailer instanceof Mailer)
            return $this->getMailerConfig();
        return $this->mailer;
    }

    /**
     * @param FormInterface $form
     * @param Request $request
     *
     * @return null
     */
    public function handleMailerRequest(FormInterface $form, Request $request, $formName = 'install_mailer')
    {
        if (! $form->isSubmitted())
            $form->handleRequest($request);

        $this->mailerSaved = false;

        if (! $form->isSubmitted())
            return;

        if ($form->isValid()) {
            foreach ($request->get($formName) as $name => $value) {
                switch ($name) {
                    case 'transport':
                    case 'user':
                    case 'password':
                    case 'senderName':
                    case 'senderAddress':
                    case 'host':
                    case 'encryption':
                    case 'auth_mode':
                        $name = explode('_', $name);
                        foreach ($name as $q => $w)
                            $name[$q] = ucfirst($w);
                        $name = implode('', $name);
                        $set = 'set' . ucfirst($name);

                        $this->getMailer()->$set($value);
                        break;
                    case 'port':
                        $name = explode('_', $name);
                        foreach ($name as $q => $w)
                            $name[$q] = ucfirst($w);
                        $name = implode('', $name);
                        $set = 'set' . ucfirst($name);

                        $this->getMailer()->$set(intval($value));
                        break;
                }
            }

            if ($this->mailer->getHost() === 'empty' || empty($this->mailer->getHost()))
                $this->mailer->setHost(null);

            $this->saveMailerConfig($this->mailer);
        }

        return;
    }

    /**
     * @return bool
     */
    public function isMailerSaved(): bool
    {
        return $this->mailerSaved;
    }

    /**
     * @param \Twig_Environment $twig
     * @param MessageManager $messages
     * @param \Swift_Mailer $swiftMailer
     */
    public function testDelivery(\Twig_Environment $twig, MessageManager $messages, \Swift_Mailer $swiftMailer)
    {
        $this->getMailer()->setCanDeliver(false);

        $messages->setDomain('Install');

        if ($this->getMailer()->getTransport() != '')
        {
            $email= (new \Swift_Message($twig->render('Emails/test_header.html.twig', ['direct' => true])))
                ->setFrom($this->getMailer()->getSenderAddress(), $this->getMailer()->getSenderName())
                ->setTo($this->getMailer()->getSenderAddress(), $this->getMailer()->getSenderName())
                ->setBody(
                    $twig->render(
                        'Emails/test.html.twig'
                    ),
                    'text/html'
                )
            ;
            $this->getMailer()->setCanDeliver(true);

            try
            {
                $swiftMailer->send($email);
            }
            catch (\Swift_TransportException $e)
            {
                $messages->add('error', $e->getMessage());
                $this->getMailer()->setCanDeliver(false);
            }
            catch (\Swift_RfcComplianceException $e)
            {
                $messages->add('error', $e->getMessage());
                $this->getMailer()->setCanDeliver(false);
            }
            if ($this->getMailer()->isCanDeliver())
            {
                $spool = $swiftMailer->getTransport()->getSpool();
                $transport = new \Swift_SmtpTransport($this->getMailer()->getHost(), $this->getMailer()->getPort(), $this->getMailer()->getEncryption());
                $transport
                    ->setUsername($this->getMailer()->getUser())
                    ->setPassword($this->getMailer()->getPassword())
                ;
                $ok = true;

                try
                {
                    $spool->flushQueue($transport);
                } catch (\Exception $e) {
                    $messages->add('warning', 'mailer.delivered.failed', ['%{address}' => $this->getMailer()->getSenderAddress(), '%{message}' => $e->getMessage()]);
                    $ok = false;
                }
                if ($ok)
                {
                    $messages->add('success', 'mailer.delivered.success', ['%{email}' => $this->getMailer()->getSenderAddress()]);

                    $email = (new \Swift_Message($twig->render('Emails/test_header.html.twig', ['direct' => false])))
                        ->setFrom($this->getMailer()->getSenderAddress(), $this->getMailer()->getSenderName())
                        ->setTo($this->getMailer()->getSenderAddress(), $this->getMailer()->getSenderName())
                        ->setBody(
                            $twig->render(
                                'Emails/test_spool.html.twig'
                            ),
                            'text/html'
                        )
                    ;
                    $swiftMailer->send($email);
                }
            }
        }

        return $this->mailer->isCanDeliver();
    }
}