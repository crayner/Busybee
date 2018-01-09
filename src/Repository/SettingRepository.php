<?php
namespace App\Repository;

use App\Entity\Setting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class SettingRepository extends ServiceEntityRepository
{
	/**
	 * SettingRepository constructor.
	 *
	 * @param RegistryInterface $registry
	 */
	public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Setting::class);
    }

	/**
	 * @param $name
	 *
	 * @return mixed
	 */
	public function loadOneByName($name)
	{
		return $this->createQueryBuilder('s')
			->where('s.name = :name')
			->setParameter('name', $name)
			->getQuery()
			->getOneOrNullResult();
	}
}
