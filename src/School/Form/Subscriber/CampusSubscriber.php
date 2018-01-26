<?php
namespace App\School\Form\Subscriber;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CampusSubscriber implements EventSubscriberInterface
{
	/**
	 * @var string
	 */
	private $country;

	/**
	 * @return array
	 */
	public static function getSubscribedEvents()
	{
		return array(
			FormEvents::PRE_SUBMIT   => 'preSubmit',
			FormEvents::PRE_SET_DATA => 'preSetData',
		);
	}

	public function __contruct($country)
	{
		$this->country = $country;
	}

	/**
	 * @param FormEvent $event
	 */
	public function preSubmit(FormEvent $event)
	{
		$data = $event->getData();

		$data['identifier'] = strtoupper($data['identifier']);

		$event->setData($data);
	}

	/**
	 * @param FormEvent $event
	 */
	public function preSetData(FormEvent $event)
	{
		$data = $event->getData();

		$data->setCountry(empty($data->getCountry()) ? $this->country : $data->getCountry());

		$event->setData($data);
	}
}