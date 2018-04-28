<?php
namespace App\Timetable\Form\Subscriber;

use App\Core\Manager\SettingManager;
use App\Entity\TimetablePeriod;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ColumnSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var array
     */
    private $periods;

    /**
     * ColumnSubscriber constructor.
     * @param SettingManager $settingManager
     */
    public function __construct(SettingManager $settingManager)
    {
        $this->entityManager = $settingManager->getEntityManager();
        $this->periods = $settingManager->get('schoolday.periods');
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

        if ($data->getColumns()->count() > 0) {
            foreach ($data->getColumns() as $column) {
                if ($column->getPeriods()->count() == 0) {
                    foreach ($this->periods as $name => $val) {
                        $period = new TimetablePeriod();
                        $period->setName($name);
                        $period->setCode($val['code']);
                        $period->setStart(new \DateTime($val['start']));
                        $period->setEnd(new \DateTime($val['end']));
                        $period->setColumn($column);
                        $this->entityManager->persist($period);
                        $this->entityManager->flush();
                        $column->addPeriod($period);
                    }
                }

            }
        }

        $event->setData($data);
    }
}