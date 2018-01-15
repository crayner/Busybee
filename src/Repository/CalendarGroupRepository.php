<?php
namespace App\Repository;

use App\Entity\CalendarGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CalendarGroupRepository extends ServiceEntityRepository
{
	/**
	 * CalendarGroupRepository constructor.
	 *
	 * @param RegistryInterface $registry
	 */
	public function __construct(RegistryInterface $registry)
	{
		parent::__construct($registry, CalendarGroup::class);
	}
}
