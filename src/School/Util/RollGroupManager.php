<?php
namespace App\School\Util;

use App\Calendar\Util\CalendarManager;
use App\Entity\Calendar;
use App\Entity\RollGroup;
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
	public function getTutorNames(RollGroup $entity): string
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

    /**
     * @param RollGroup $rollGroup
     * @return null|string
     */
    public function getDetails(RollGroup $rollGroup): ?string
    {
        $result = '';
        $result .= $rollGroup->getWebsite() ?: '';
        $result = $rollGroup->getNextRoll() ? $this->injectNewLine($result) . $rollGroup->getNextRoll()->getFullName() : '' ;

        return $result;
    }

    /**
     * @param string $result
     * @return string
     */
    private function injectNewLine(string $result): string
    {
        if (!empty($result) && mb_substr($result, -6) !== '<br />')
            $result .= '<br />';
        return $result;
    }

    public function getCalendar(): Calendar
    {
        return $this->calendar;
    }
}