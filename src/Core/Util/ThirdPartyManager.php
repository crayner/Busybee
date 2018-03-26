<?php
namespace App\Core\Util;

use App\Core\Manager\MailerManager;
use App\Core\Manager\SettingManager;
use App\Core\Manager\TabManager;
use App\Core\Organism\ThirdParty;
use Symfony\Component\Yaml\Yaml;

class ThirdPartyManager extends TabManager
{
    /**
     * @var SettingManager
     */
    private $settingManager;

    /**
     * @var ThirdParty
     */
    private $entity;

    /**
     * @var MailerManager
     */
    private $mailerManager;

    /**
     * ThirdPartyManager constructor.
     * @param SettingManager $settingManager
     */
    public function __construct(SettingManager $settingManager)
    {
        $this->settingManager = $settingManager;
        $this->mailerManager = new MailerManager($settingManager->getParameter('kernel.project_dir'));
    }

    /**
     * @return array
     */
    public function getTabs(): array
    {
        return Yaml::parse("
google:
    label: third.party.google.label
    include: Setting/google.html.twig
    translation: System
    with: 
        panelStyle: success
    display: ''
email:
    label: third.party.email.label
    include: Setting/email.html.twig
    translation: System
    with: 
        panelStyle: warning
    display: ''
");
    }

    /**
     * @return ThirdParty
     */
    public function getEntity(): ThirdParty
    {
        return $this->entity ?: $this->setEntity(null)->getEntity();
    }

    /**
     * @param ThirdParty|null $entity
     * @return ThirdPartyManager
     * @throws \Exception
     */
    public function setEntity(?ThirdParty $entity): ThirdPartyManager
    {
        if (! $entity instanceof ThirdParty) {
            $configDir = $this->getSettingManager()->getParameter('kernel.project_dir'). '/config';
            try {
                $mailer = Yaml::parse(file_get_contents($configDir . '/packages/swiftmailer.yaml'));
            } catch (\Exception $e) {
                throw $e;
            }
            $entity = new ThirdParty($mailer);
            $google = $this->getSettingManager()->get('google');
            $entity->setGoogleOAuth($google['o_auth'] ? true : false);
            $entity->setGoogleClientId($google['client_id']);
            $entity->setGoogleClientSecret($google['client_secret']);
        }

        $this->entity =  $entity;
        return $this;
    }

    /**
     * @return SettingManager
     */
    public function getSettingManager(): SettingManager
    {
        return $this->settingManager;
    }

    /**
     * @return ThirdParty
     */
    public function getMailer(): ThirdParty
    {
        return $this->getEntity();
    }

    /**
     * @param $data
     * @return ThirdPartyManager
     */
    public function saveGoogle(): ThirdPartyManager
    {
        $google = [];
        $google['o_auth'] = $this->getEntity()->isGoogleOAuth();
        $google['client_id'] = $this->getEntity()->getGoogleClientId();
        $google['client_secret'] = $this->getEntity()->getGoogleClientSecret();
        $this->getSettingManager()->set('google', $google);

        return $this;
    }

    /**
     * @return MailerManager
     */
    public function getMailerManager(): MailerManager
    {
        return $this->mailerManager;
    }
}