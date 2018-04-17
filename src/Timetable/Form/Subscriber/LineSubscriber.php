<?php
namespace App\Timetable\Form\Subscriber;

use App\Entity\Activity;
use Doctrine\ORM\EntityRepository;
use Hillrange\Form\Type\CollectionEntityType;
use Hillrange\Form\Type\CollectionType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class LineSubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that you want to listen on the form.pre_submit
        // event and that the preSubmit method should be called.
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
        );
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (!empty($data->getCalendar())) {
            $calendar = $data->getCalendar();
            $form->add('activities', CollectionType::class, [
                    'help' => 'line.activities.help',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'label' => 'line.activities.label',
                    'entry_type' => CollectionEntityType::class,
                    'entry_options' => [
                        'class' => Activity::class,
                        'choice_label' => 'name',
                        'query_builder' => function (EntityRepository $er) use ($calendar) {
                            return $er->createQueryBuilder('a')
                                ->leftJoin('a.calendarGrades', 'cg')
                                ->leftJoin('cg.calendar', 'c')
                                ->orderBy('a.name', 'ASC')
                                ->where('c.id = :calendar_id')
                                ->setParameter('calendar_id', $calendar->getId());
                        },
                        'placeholder' => 'line.activities.placeholder',
                        'translation_domain' => 'Timetable',
                        'data_class' => null,
                        'block_prefix' => 'activities',
                    ],
                ]
            );

        }

        $event->setData($data);
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();

        $data['code'] = empty($data['code']) ? '' : preg_replace('/\s/', '', strtoupper($data['code']));

        $event->setData($data);
    }
}