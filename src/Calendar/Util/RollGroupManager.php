<?php
namespace App\Calendar\Util;

use App\Entity\Calendar;
use Doctrine\ORM\EntityManagerInterface;

class RollGroupManager
{
	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;

	/**
	 * @var Calendar
	 */
	private $calendar;

	/**
	 * RollGroupManager constructor.
	 *
	 * @param EntityManagerInterface $entityManager
	 * @param CalendarManager          $calendarManager
	 */
	public function __construct(EntityManagerInterface $entityManager, CalendarManager $calendarManager)
	{
		$this->entityManager   = $entityManager;
		$this->calendar = $calendarManager->getCurrentCalendar();
	}

	/**
	 * @return string
	 */
	public function getTutorNames(CalendarGroup $entity): string
	{
		if (empty($entity))
			return '';

		$names = '';

		if (!empty($entity->getTutor1() && $entity->getTutor1() instanceof Staff))
			$names .= $entity->getTutor1()->formatName();

		if (!empty($entity->getTutor2() && $entity->getTutor2() instanceof Staff))
			$names .= "<br />" . $entity->getTutor2()->formatName();

		if (!empty($entity->getTutor3() && $entity->getTutor3() instanceof Staff))
			$names .= "<br />" . $entity->getTutor3()->formatName();

		return $names;
	}

	/**
	 * @return EntityManagerInterface
	 */
	public function getEntityManager(): EntityManagerInterface
	{
		return $this->entityManager;
	}
}