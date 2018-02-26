<?php
namespace App\Core\Subscriber;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SequenceSubscriber implements EventSubscriberInterface
{
	/**
	 * @return array
	 */
	public static function getSubscribedEvents()
	{
		// Tells the dispatcher that you want to listen on the form.pre_set_data
		// event and that the preSetData method should be called.
		return [
			FormEvents::PRE_SUBMIT => 'preSubmit',
		];
	}

	/**
	 * @param FormEvent $event
	 */
	public function preSubmit(FormEvent $event)
	{
		$data = $event->getData();

		if (empty($data))
		    return ;
		$s = 0;
		foreach($data as $q=>$w)
		    if (isset($w['sequence']) && $w['sequence'] > $s)
		        $s = $w['sequence'];

		$s = $s > 100 ? 1 : 101 ;

        foreach($data as $q=>$w)
            if (isset($w['sequence']))
                $data[$q]['sequence'] = $s++;

        $event->setData($data);
	}
}