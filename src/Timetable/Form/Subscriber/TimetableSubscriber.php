<?php
namespace App\Timetable\Form\Subscriber;

use App\Core\Manager\SettingManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class TimetableSubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that you want to listen on the form.pre_submit
        // event and that the preSubmit method should be called.
        return [
            FormEvents::PRE_SUBMIT => 'beforeSubmit',
        ];
    }

    public function beforeSubmit(FormEvent $event)
    {
        $data = $event->getData();
        if (empty($data['columns'])){
            $week = $this->settingManager->get('schoolweek');
            $seq = 1;
            foreach($week as $full=>$abbr)
            {
                $column = [];
                $column['name'] = $full;
                $column['code'] = $abbr;
                $column['sequence'] = strval($seq++);
                $column['timetable'] = $this->settingManager->getRequestParameter('id');
                $data['columns'][] = $column;
            }

            $event->setData($data);
        }
    }

    /**
     * @var SettingManager
     */
    private $settingManager;

    /**     * TimetableSubscriber constructor.
     * @param SettingManager $settingManager
     */
    public function __construct(SettingManager $settingManager)
    {
        $this->settingManager = $settingManager;
    }
}