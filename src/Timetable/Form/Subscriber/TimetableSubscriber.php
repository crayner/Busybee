<?php
namespace App\Timetable\Form\Subscriber;

use App\Core\Manager\SettingManager;
use App\Entity\TimetableDay;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class TimetableSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface 
     */
    private $entityManager;

    /**
     * @var array
     */
    private $days;

    /**
     * TimetableSubscriber constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(SettingManager $settingManager)
    {
        $this->entityManager =  $settingManager->getEntityManager();
        $this->days = $settingManager->get('schoolweek');
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that you want to listen on the form.pre_submit
        // event and that the preSubmit method should be called.
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();

        if (count($this->days) != $data->getDays()->count() && count($this->days) > 0) {
            foreach ($this->days as $day) {
                $set = true;

                foreach ($data->getDays() as $d)
                    if ($d->getName() == $day)
                        $set = false;

                if ($set) {
                    $td = new TimetableDay();
                    $td->setName($day);
                    $td->setDayType(true);
                    $data->addDay($td);
                }
            }
        }

        $event->setData($data);
    }
}