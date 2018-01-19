<?php
namespace App\Core\Subscriber;

use App\Entity\Phone;
use App\Repository\PhoneRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PhoneSubscriber implements EventSubscriberInterface
{
	private $pr;

	public function __construct(PhoneRepository $pr)
	{
		$this->pr = $pr;
	}

	/**
	 * @return array
	 */
	public static function getSubscribedEvents()
	{
		// Tells the dispatcher that you want to listen on the form.pre_submit
		// event and that the preSubmit method should be called.
		return array(FormEvents::PRE_SUBMIT => 'preSubmit');
	}

	/**
	 * @param FormEvent $event
	 */
	public function preSubmit(FormEvent $event)
	{
		$data = $event->getData();
		$form = $event->getForm();

		$entity = $this->pr->findOneByPhoneNumber(preg_replace('/\D/', '', $data['phoneNumber']));

		if ($entity instanceof Phone)
			$form->setData($entity);

	}
}