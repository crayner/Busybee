<?php
namespace App\Install\Form\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class MailerSubscriber implements EventSubscriberInterface
{

	/**
	 * @return array
	 */
	public static function getSubscribedEvents()
	{
		// Tells the dispatcher that you want to listen on the form.pre_submit
		// event and that the preSubmit method should be called.
		return array(
			FormEvents::PRE_SUBMIT => 'preSubmit',
		);
	}

	/**
	 * @param FormEvent $event
	 */
	public function preSubmit(FormEvent $event)
	{
		$data = $event->getData();

		if (!empty($data['transport']))
			switch ($data['transport'])
			{
				case 'gmail':
					$data['host'] = 'smtp.gmail.com';
					break;
				case 'smtp':
					break;
				default:
					$data['host'] = 'empty';
			}

		$event->setData($data);

	}
}