<?php
namespace App\Address\Util;

use App\Core\Manager\MessageManager;
use App\Core\Manager\SettingManager;
use App\Entity\Address;
use App\Entity\Family;
use App\Entity\Person;
use Doctrine\ORM\EntityManagerInterface;

class AddressManager
{
	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;

	/**
	 * @var Address
	 */
	private $address;

	/**
	 * @var MessageManager
	 */
	private $messageManager;

	/**
	 * @var SettingManager
	 */
	private $settingManager;

	/**
	 * AddressManager constructor.
	 *
	 * @param EntityManagerInterface $entityManager
	 */
	public function __construct(EntityManagerInterface $entityManager, MessageManager $messageManager, SettingManager $settingManager)
	{
		$this->entityManager = $entityManager;
		$this->messageManager = $messageManager;
		$this->settingManager = $settingManager;
	}

	/**
	 * @param $id
	 *
	 * @return Address
	 */
	public function find($id): Address
	{
		return $this->checkAddress($this->entityManager->getRepository(Address::class)->find($id));
	}

	/**
	 * @param Address|null $address
	 *
	 * @return Address
	 */
	private function checkAddress(Address $address = null): Address
	{
		if ($address instanceof Address)
		{
			$this->address = $address;

			return $this->address;
		}
		if ($this->address instanceof Address)
			return $this->address;

		$this->address = new Address();

		return $this->address;
	}
	
	/**
	 * can Delete
	 *
	 * @return boolean
	 */
	public function canDelete(Address $address = null): bool
	{
		$this->checkAddress($address);

		$x = $this->entityManager->getRepository(Person::class)->createQueryBuilder('p')
			->select('COUNT(p.id)')
			->where('p.address1 = :address1')
			->orWhere('p.address2 = :address2')
			->setParameter('address1', $this->address->getId())
			->setParameter('address2', $this->address->getId())
			->getQuery()
			->getSingleScalarResult();
		if (! empty($x))
			return false;

		if ($this->entityManager->getMetadataFactory()->hasMetadataFor(Family::class))
		{
			$x = $this->entityManager->getRepository(Family::class)->createQueryBuilder('f')
				->select('COUNT(f.id)')
				->where('f.address1 = :address1')
				->orWhere('f.address2 = :address2')
				->setParameter('address1', $this->address->getId())
				->setParameter('address2', $this->address->getId())
				->getQuery()
				->getSingleScalarResult();
			if (! empty($x))
				return false;
		}

		return true;
	}

	/**
	 * @return MessageManager
	 */
	public function getMessageManager(): MessageManager
	{
		return $this->messageManager;
	}

	/**
	 * Format Address
	 *
	 * @version    8th November 2016
	 * @since      8th November 2016
	 *
	 * @param    null|Address $address
	 *
	 * @return    html string
	 */
	public function formatAddress(Address $address = null)
	{
		if ($address instanceof Address)
			$data = array('propertyName' => $address->getPropertyName(),
			              'streetName'   => $address->getStreetName(), 'locality' => $address->getLocality()->getName(), 'territory' => $address->getLocality()->getTerritory(),
			              'postCode'     => $address->getLocality()->getPostCode(), 'country' => $address->getLocality()->getCountryName(),
			              'buildingType' => $address->getBuildingType(), 'buildingNumber' => $address->getBuildingNumber(), 'streetNumber' => $address->getStreetNumber());
		else
			$data = array('propertyName' => null,
			              'streetName'   => null, 'locality' => null, 'territory' => null,
			              'postCode'     => null, 'country' => null,
			              'buildingType' => null, 'buildingNumber' => null, 'streetNumber' => null);

		return $this->settingManager->get('address.format', null, $data);
	}

	/**
	 * get Address List Label
	 *
	 * @version    8th November 2016
	 * @since      8th November 2016
	 *
	 * @param    Address|null $address
	 *
	 * @return    html string
	 */
	public function getAddressListLabel(Address $address = null)
	{
		if ($address instanceof Address)
			$data = ['propertyName' => $address->getPropertyName(), 'streetName' => $address->getStreetName(),
			         'buildingType' => $address->getBuildingType(), 'buildingNumber' => $address->getBuildingNumber(),
			         'streetNumber' => $address->getStreetNumber(), 'locality' => $address->getLocality()->getName()];
		else
			$data = ['propertyName'   => null, 'streetName' => null, 'buildingType' => null,
			         'buildingNumber' => null, 'streetNumber' => null, 'locality' => null];

		return trim($this->settingManager->get('address.list.label', null, $data));
	}
}