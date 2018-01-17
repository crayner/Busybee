<?php
namespace App\Calendar\Util;

use App\Entity\Calendar;
use App\Entity\CalendarGroup;
use Doctrine\ORM\EntityManagerInterface;

class CalendarGroupManager
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
	 * CalendarGroupManager constructor.
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
	 * @return mixed
	 */
	public function getYearCalendarGroups()
	{
		return $this->entityManager->getRepository(CalendarGroup::class)->createQueryBuilder('g')
			->leftJoin('g.calendar', 'c')
			->where('c.id = :calendar_id')
			->setParameter('calendar_id', $this->calendar->getId())
			->orderBy('g.sequence', 'ASC')
			->getQuery()
			->getResult();
	}

	/**
	 * Delete Student CalendarGroup
	 *
	 * @param $id
	 *
	 * @return array
	 */
	public function deleteStudentCalendarGroup($id)
	{
		$status            = [];
		$status['message'] = 'student.calendar.group.notfound';
		$status['status']  = 'warning';
		if (intval($id < 1))
			return $status;

		$entity = $this->entityManager->getRepository(StudentCalendarGroup::class)->find($id);

		if (is_null($entity))
			return $status;

		if (!$entity->canDelete())
		{
			$status            = [];
			$status['message'] = 'student.calendar.group.remove.blocked';
			$status['status']  = 'warning';

			return $status;
		}
		try
		{
			$this->entityManager->remove($entity);
			$this->entityManager->flush();
		}
		catch (\Exception $e)
		{
			$status            = [];
			$status['message'] = 'student.grade.remove.fail';
			$status['status']  = 'error';

			return $status;
		}
		$status            = [];
		$status['message'] = 'student.grade.remove.success';
		$status['status']  = 'success';

		return $status;
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
	 * @return string
	 */
	public function getSpaceName(CalendarGroup $entity): string
	{
		if (empty($entity))
			return '';

		if ($entity->getSpace() instanceof Space)
			return $entity->getSpace()->getName();

		return '';
	}

	/**
	 * @param int|null $id
	 *
	 * @return CalendarGroup|null
	 */
	public function getEntity(int $id = null): ?CalendarGroup
	{
		return $this->getEntityManager()->getRepository(CalendarGroup::class)->find($id);
	}

	/**
	 * @return EntityManagerInterface
	 */
	public function getEntityManager(): EntityManagerInterface
	{
		return $this->entityManager;
	}

	/**
	 * @return bool
	 */
	public function isStudentInstalled(): bool
	{
		if (class_exists('Busybee\People\StudentBundle\Entity\StudentCalendarGroup'))
		{
			$metaData = $this->getentityManager()->getClassMetadata('\Busybee\People\StudentBundle\Entity\StudentCalendarGroup');
			$schema   = $this->getentityManager()->getConnection()->getSchemaManager();

			return $schema->tablesExist([$metaData->table['name']]);
		}

		return false;
	}

	/**
	 * @return bool
	 */
	public function isStaffInstalled(): bool
	{
		if (class_exists('Busybee\People\StaffBundle\Entity\Staff'))
		{
			$metaData = $this->getentityManager()->getClassMetadata('\Busybee\People\StaffBundle\Entity\Staff');
			$schema   = $this->getentityManager()->getConnection()->getSchemaManager();

			return $schema->tablesExist([$metaData->table['name']]);
		}

		return false;
	}
}