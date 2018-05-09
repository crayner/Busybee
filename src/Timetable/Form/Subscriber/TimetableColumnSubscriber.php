<?php
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
            if (empty($data->getStart()) || $data->getStart()->format('H:i') === '00:00')
                if ($this->settingManager->get('schoolday.begin') instanceof \DateTime)
                    $data->setStart($this->settingManager->get('schoolday.begin'));
                else
                    $data->setStart(new \DateTime($this->settingManager->get('schoolday.begin')));

            if (empty($data->getEnd()) || $data->getEnd()->format('H:i') === '00:00')
                if ($this->settingManager->get('schoolday.finish') instanceof \DateTime)
                    $data->setEnd($this->settingManager->get('schoolday.finish'));
                else
                    $data->setEnd(new \DateTime($this->settingManager->get('schoolday.finish')));
        }

        $event->setData($data);
    }
}