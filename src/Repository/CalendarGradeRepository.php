<?php
namespace App\Repository;

use App\Entity\CalendarGrade;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CalendarGradeRepository extends ServiceEntityRepository
{
	/**
	 * CalendarRepository constructor.
	 *
	 * @param RegistryInterface $registry
	 */
	public function __construct(RegistryInterface $registry)
	{
		parent::__construct($registry, CalendarGrade::class);
	}
}
