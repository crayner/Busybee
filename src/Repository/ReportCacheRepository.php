<?php
namespace App\Repository;

use App\Entity\ReportCache;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ReportCacheRepository extends ServiceEntityRepository
{
	/**
	 * ReportCacheRepository constructor.
	 *
	 * @param RegistryInterface $registry
	 */
	public function __construct(RegistryInterface $registry)
	{
		parent::__construct($registry, ReportCache::class);
	}
}
