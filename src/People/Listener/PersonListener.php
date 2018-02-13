<?php
/**
 * Created by PhpStorm.
 * User: craig
 * Date: 14/02/2018
 * Time: 09:26
 */

namespace App\People\Listener;

use App\Core\Manager\SettingManager;
use App\Entity\Setting;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use App\Entity\Person;
use Doctrine\Common\EventSubscriber;

class PersonListener implements EventSubscriber
{
    /**
     * @var SettingManager
     */
    private $settingManager;

    /**
     * SearchIndexerSubscriber constructor.
     * @param SettingManager $settingManager
     */
    public function __construct(SettingManager $settingManager)
    {
        $this->settingManager = $settingManager;
    }

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            'prePersist',
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (! $entity instanceof Person)
            return ;

        if (! $this->settingManager->has('person.identifier.index'))
        {
            $index = date('ym') . '00AA';
            $setting = new Setting();
            $setting->setName('person.identifier.index');
            $setting->setType('system');
            $setting->setValue($index);
            $setting->setRole('ROLE_USER');
            $setting->setDescription('Tracks the current ');
            $this->settingManager->createSetting($setting);
        }
        else {
            $index = $this->incrementIndex($this->settingManager->get('person.identifier.index'));
        }

        if (mb_substr($index, 0, 4) !== date('ym'))
            $index = date('ym') . '00AA';

        $index = $this->incrementIndex(mb_substr($index, 4));

        $entity->setIdentifier(mb_substr($entity->getSurname(), 0, 1) . '_' . date('ym') .  $index);

        $this->settingManager->set('person.identifier.index', date('ym') . $index);

        return;
    }

    /**
     * @param string $index
     * @return string
     */
    private function incrementIndex(string $index): string
    {
        $index = mb_substr($index, 0, 3) . chr(ord(mb_substr($index,3)) + 1);
        if (mb_substr($index,3) === '[')
        {
            $index = mb_substr($index, 0, 2) . chr(ord(mb_substr($index, 2, 1)) + 1) . 'A';
            if (mb_substr($index,2,1) === '[')
            {
                $index = str_replace('[', 'A', $index);
                $index = strval(intval($index) + 1) . mb_substr($index, -2);
            }
        }
        return $index;
    }
}