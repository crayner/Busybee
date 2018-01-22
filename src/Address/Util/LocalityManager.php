<?php
namespace App\Address\Util;


use App\Core\Manager\MessageManager;
use App\Entity\Address;
use App\Entity\Locality;
use Doctrine\ORM\EntityManagerInterface;

class LocalityManager
{
	/**
	 * @var EntityManagerInterface 
	 */
	private $entityManager;

	/**
	 * @var MessageManager 
	 */
	private $messageManager;

	/**
	 * @var Locality
	 */
	private $locality;

	/**
	 * LocalityManager constructor.
	 *
	 * @param EntityManagerInterface $entityManager
	 * @param MessageManager         $messageManager
	 */
	public function __construct(EntityManagerInterface $entityManager, MessageManager $messageManager)
	{
		$this->entityManager = $entityManager;
		$this->messageManager = $messageManager;
		$this->messageManager->setDomain('Person');
	}

	/**
	 * @param $id
	 *
	 * @return Locality
	 */
	public function find($id): Locality
	{
		return $this->checkLocality($this->entityManager->getRepository(Locality::class)->find($id));
	}

	/**
	 * Check Locality
	 *
	 * @param Locality|null $locality
	 *
	 * @return Locality
	 */
	private function checkLocality(Locality $locality = null): Locality
	{
		if ($locality instanceof Locality)
		{
			$this->locality = $locality;

			return $this->locality;
		}
		if ($this->locality instanceof Locality)
			return $this->locality;

		$this->locality = new Locality();

		return $this->locality;
	}

	/**
	 * @return MessageManager
	 */
	public function getMessageManager(): MessageManager
	{
		return $this->messageManager;
	}

	/**
	 * Can Delete
	 *
	 * @param Locality|null $locality
	 **
	 * @return bool
	 */
	public function canDelete(Locality $locality = null)
	{
		$this->checkLocality($locality);

		if (intval($this->locality->getId()) < 1)
			return false;

		$result = $this->entityManager->getRepository(Address::class)->createQueryBuilder('a')
			->leftJoin('a.locality', 'l')
			->where('l.id = :loc_id')
			->setParameter('loc_id', $this->locality->getId())
			->getQuery()
			->getResult();;
		if (!empty($result))
			return false;

		return true;
	}
}