<?php
/**
 * Created by PhpStorm.
 * User: craig
 * Date: 3/04/2018
 * Time: 12:02
 */

namespace App\Timetable\Form\Subscriber;


use App\Core\Manager\SettingManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class TimetableColumnSubscriber implements EventSubscriberInterface
{
    /**
     * @var SettingManager
     */
    private $settingManager;

    /**
     * ColumnSubscriber constructor.
     * @param SettingManager $settingManager
     */
    public function __construct(SettingManager $settingManager)
    {
        $this->settingManager = $settingManager;
    }
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that you want to listen on the form.pre_submit
        // event and that the preSubmit method should be called.
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
        );
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();

        if (!is_null($data)) {
            if ($data->getStart()->format('H:i') === '00:00')
                $data->setStart(new \DateTime($this->settingManager->get('SchoolDay.Begin')));

            if ($data->getEnd()->format('H:i') === '00:00')
                $data->setEnd(new \DateTime($this->settingManager->get('SchoolDay.Finish')));
        }

        $event->setData($data);
    }
}