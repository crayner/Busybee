<?php
namespace App\School\Form\Subscriber;

use App\School\Util\HouseManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class HouseSubscriber implements EventSubscriberInterface
{
	/**
	 * @var HouseManager
	 */
	private $houseManager;

	/**
	 * HouseSubscriber constructor.
	 *
	 * @param HouseManager $houseManager
	 */
	public function __construct(HouseManager $houseManager)
	{
		$this->houseManager = $houseManager;
	}

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
		$data   = $event->getData();
		$entity = $event->getForm()->getData();

		if (!is_null($entity) && $data['name'] != $entity->getName())
		{
			if ($this->houseManager->getStatus($entity) > 0)
			{
				$error = new FormError('school.house.rename.locked');
				$event->getForm()->get('name')->addError($error);
			}
		}

		if (!is_null($entity) && !file_exists($entity->getLogo()))
			$entity->setLogo(null, false);

		$event->setData($data);
		$event->getForm()->setData($entity);
	}
}