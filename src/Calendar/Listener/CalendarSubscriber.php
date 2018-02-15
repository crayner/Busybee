<?php
namespace App\Calendar\Listener;

use App\Calendar\Util\CalendarManager;
use App\Entity\CalendarGroup;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CalendarSubscriber implements EventSubscriberInterface
{
	/**
	 * @var CalendarManager
	 */
	private $calendarManager;

	/**
	 * YearSubscriber constructor.
	 *
	 * @param CalendarManager $ym
	 */
	public function __construct(CalendarManager $ym)
	{
		$this->calendarManager = $ym;
	}

	/**
	 * @return array
	 */
	public static function getSubscribedEvents()
	{
		return [
			FormEvents::PRE_SUBMIT => 'preSubmit',
		];
	}

	/**
	 * @param FormEvent $event
	 */
	public function preSubmit(FormEvent $event)
	{
		$data   = $event->getData();
		$form   = $event->getForm();
		$entity = $form->getData();

		$specDays = [];
		if (isset($data['specialDays']) && !empty($entity->getSpecialDays()) && $entity->getSpecialDays()->count() > 0)
		{
			foreach ($entity->getSpecialDays() as $key => $sd)
			{
				$delete = true;
				foreach ($data['specialDays'] as $q => $nsd)
				{
					$day = $nsd['day'];
					if ($sd->getDay()->format('Ymd') == $day['year'] . str_pad($day['month'], 2, '0', STR_PAD_LEFT) . str_pad($day['day'], 2, '0', STR_PAD_LEFT))
					{
						$delete         = false;
						$specDays[$key] = $nsd;
						unset($data['specialDays'][$q]);
						break;
					}
					if ($delete)
					{
						$entity->getSpecialDays()->remove($key);
						$this->calendarManager->getEntityManager()->remove($sd);
						$this->calendarManager->getEntityManager()->flush();
					}
				}
			}
		}
		if (!empty($data['specialDays']))
			$specDays = array_merge($specDays, $data['specialDays']);

		if (!empty($specDays))
			$data['specialDays'] = $specDays;

		if (!empty($entity->getDownloadCache()) && file_exists($entity->getDownloadCache()))
			unlink($entity->getDownloadCache());

		$event->setData($data);
		$form->setData($entity);
	}
}