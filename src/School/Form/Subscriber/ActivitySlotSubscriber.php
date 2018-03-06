<?php
namespace App\School\Form\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ActivitySlotSubscriber implements EventSubscriberInterface
{

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that you want to listen on the form.pre_submit
        // event and that the preSubmit method should be called.
        return array(
            FormEvents::PRE_SUBMIT => 'beforeSubmit',
            FormEvents::PRE_SET_DATA => 'beforeData',
        );
    }

    /**
     * @param FormEvent $event
     */
    public function beforeSubmit(FormEvent $event)
    {
        $data = $event->getData();

        if ($data['type'] == 1)
            $data['space'] = null;
        else
            $data['externalLocation'] = null;
        dump($data);
        $event->setData($data);
    }

    /**
     * @param FormEvent $event
     */
    public function beforeData(FormEvent $event)
    {
        $entity = $event->getData();
        if (null === $entity)
            return ;

        $entity->setType('0');

        $space = $entity->getSpace();
        $el = $entity->getExternalLocation();

        if (empty($space) && ! empty($el))
            $entity->setType('1');

        $event->setData($entity);
    }
}