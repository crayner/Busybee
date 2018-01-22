<?php
namespace App\Address\Form\Subscriber;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AddressSubscriber implements EventSubscriberInterface
{
	/**
	 * @return array
	 */
	public static function getSubscribedEvents()
	{
		// Tells the dispatcher that you want to listen on the form.pre_set_data
		// event and that the preSetData method should be called.
		return array(FormEvents::PRE_SUBMIT => 'preSubmit');
	}

	/**
	 * @param FormEvent $event
	 */
	public function preSubmit(FormEvent $event)
	{
		$data = $event->getData();
		$form = $event->getForm();

		if (empty($data['streetNumber']) && intval($data['streetName']) > 0)
		{
			$num                  = intval($data['streetName']);
			$data['streetNumber'] = strval($num);
			$data['streetName']   = trim(str_replace($num, '', $data['streetName']));
		}
		$data['propertyName']   = empty($data['propertyName']) ? '' : $data['propertyName'];
		$data['streetNumber']   = empty($data['streetNumber']) ? '' : $data['streetNumber'];
		$data['buildingType']   = empty($data['buildingType']) ? '' : $data['buildingType'];
		$data['buildingNumber'] = empty($data['buildingNumber']) ? '' : $data['buildingNumber'];

		$event->setData($data);
	}
}