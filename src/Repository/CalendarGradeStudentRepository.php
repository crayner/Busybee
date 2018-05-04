<?php
namespace App\Repository;

use App\Entity\CalendarGradeStudent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CalendarGradeStudentRepository extends ServiceEntityRepository
{
	/**
	 * CalendarRepository constructor.
	 *
	 * @param RegistryInterface $registry
	 */
	public function __construct(RegistryInterface $registry)
	{
		parent::__construct($registry, CalendarGradeStudent::class);
	}
}
