<?php
namespace App\Timetable\Form\Subscriber;

use App\Core\Manager\SettingManager;
use App\Entity\TimetableColumn;
use App\Entity\TimetableDay;
use Doctrine\Common\Collections\ArrayCollection;
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
            FormEvents::PRE_SUBMIT => 'preSubmit',
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
                $set = false;

                foreach ($data->getDays() as $d) {
                    if ($d->getName() == $day)
                        $set = true;
                }
                if (!$set) {
                    $td = new TimetableDay();
                    $td->setName($day);
                    $td->setDayType(true);
                    $td->setTimetable($data);
                }
            }

        }
        $event->setData($data);
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        $offset = 500;
        $useOffSet = false;

        if (!empty($data['columns'])) {
            $cols = new ArrayCollection();
            $c = 1;
            foreach ($data['columns'] as $q => $w) {
                $column = $this->entityManager->getRepository(TimetableColumn::class)->find($w['id']);
                if (intval($w['sequence']) !== $c && intval($w['sequence']) <= 500 || $useOffSet) {
                    $w['sequence'] = $c + $offset;
                    $useOffSet = true;
                }
                else
                    $w['sequence'] = $c;

                if ($column instanceof TimetableColumn) {
                    $column->setSequence($c);
                    $cols->add($column);
                }
                $data['columns'][$q] = $w;
                $c++;
            }
            $form->get('columns')->setData($cols);
        }
        $event->setData($data);
    }
}