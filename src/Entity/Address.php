<?php
namespace App\Entity;

use App\Address\Entity\AddressExtension;

/**
 * Address
 */
class Address extends AddressExtension
{
	/**
	 * @var integer
	 */
	private $id;

	/**
	 * @var string
	 */
	private $propertyName;

	/**
	 * @var string
	 */
	private $streetName;

	/**
	 * @var string
	 */
	private $buildingType;

	/**
	 * @var string
	 */
	private $buildingNumber;

	/**
	 * @var string
	 */
	private $streetNumber;

	/**
	 * @var Locality
	 */
	private $locality;

	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Get propertyName
	 *
	 * @return string
	 */
	public function getPropertyName()
	{
		return empty($this->propertyName) ? "" : $this->propertyName;
	}

	/**
	 * Set propertyName
	 *
	 * @param string $propertyName
	 *
	 * @return Address
	 */
	public function setPropertyName($propertyName)
	{
		$this->propertyName = empty($propertyName) || is_null($propertyName) ? "" : $propertyName;

		return $this;
	}

	/**
	 * Get streetName
	 *
	 * @return string
	 */
	public function getStreetName()
	{
		return $this->streetName;
	}

	/**
	 * Set streetName
	 *
	 * @param string $streetName
	 *
	 * @return Address
	 */
	public function setStreetName($streetName)
	{
		$this->streetName = $streetName;

		return $this;
	}

	/**
	 * Get buildingType
	 *
	 * @return string
	 */
	public function getBuildingType()
	{
		if (empty($this->buildingType))
			$this->buildingType = '';

		return empty($this->buildingType) ? "" : $this->buildingType;
	}

	/**
	 * Set buildingType
	 *
	 * @param string $buildingType
	 *
	 * @return Address
	 */
	public function setBuildingType($buildingType)
	{

		$this->buildingType = empty($buildingType) ? '' : $buildingType;

		return $this;
	}

	/**
	 * Get buildingNumber
	 *
	 * @return string
	 */
	public function getBuildingNumber()
	{
		return empty($this->buildingNumber) ? "" : $this->buildingNumber;
	}

	/**
	 * Set buildingNumber
	 *
	 * @param string $buildingNumber
	 *
	 * @return Address
	 */
	public function setBuildingNumber($buildingNumber)
	{
		$this->buildingNumber = empty($buildingNumber) || is_null($buildingNumber) ? '' : $buildingNumber;

		return $this;
	}

	/**
	 * Get streetNumber
	 *
	 * @return string
	 */
	public function getStreetNumber()
	{
		return empty($this->streetNumber) ? "" : $this->streetNumber;
	}

	/**
	 * Set streetNumber
	 *
	 * @param string $streetNumber
	 *
	 * @return Address
	 */
	public function setStreetNumber($streetNumber)
	{
		$this->streetNumber = empty($streetNumber) ? '' : $streetNumber;

		return $this;
	}

	/**
	 * Get locality
	 *
	 * @return Locality
	 */
	public function getLocality()
	{
		return $this->locality;
	}

	/**
	 * Set locality
	 *
	 * @param Locality $locality
	 *
	 * @return Address
	 */
	public function setLocality(Locality $locality = null)
	{
		$this->locality = $locality;

		return $this;
	}
}
