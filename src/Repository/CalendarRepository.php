<?php
namespace App\Repository;

use App\Core\Exception\Exception;
use App\Entity\Calendar;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CalendarRepository extends ServiceEntityRepository
{
	/**
	 * CalendarRepository constructor.
	 *
	 * @param RegistryInterface $registry
	 */
	public function __construct(RegistryInterface $registry)
	{
		parent::__construct($registry, Calendar::class);
	}

	/**
	 * @return null|object
	 */
	public function loadCurrentCalendar()
	{
		$cal = $this->findOneBy(['status' => 'current']);

		if(is_null($cal))
			throw new Exception('No current calendar is available.');

		return $cal;
	}
}
