<?php
namespace App\Install\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class MiscellaneousSubscriber implements EventSubscriberInterface
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

		$secret = '';
		$chars  = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789![]{}()%&*$#^<>~@|";
		for ($i = 0; $i < 64; $i++)
			$secret .= substr($chars, rand(1, strlen($chars) + 1) - 1, 1);
		$data['secret'] = md5($secret);

		if (empty($data['username']) && !empty($data['email']))
			$data['username'] = $data['email'];


		$event->setData($data);

	}
}