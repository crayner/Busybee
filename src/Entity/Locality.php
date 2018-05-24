<?php
namespace App\Entity;

use App\Address\Entity\LocalityExtension;

/**
 * Locality
 */
class Locality extends LocalityExtension
{
	/**
	 * @var integer
	 */
	private $id;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $territory;

	/**
	 * @var string
	 */
	private $postCode;

	/**
	 * @var string
	 */
	private $country;


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
	 * Set Id
	 *
	 * @param integer $id
	 *
	 * @return Locality
	 */
	public function setId($id): Locality
	{
		$this->id = $id;
		return $this;
	}

	/**
	 * Get locality
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Set locality
	 *
	 * @param string $locality
	 *
	 * @return Locality
	 */
	public function setName($name)
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * Get territory
	 *
	 * @return string
	 */
	public function getTerritory()
	{
		return $this->territory;
	}

	/**
	 * Set territory
	 *
	 * @param string $territory
	 *
	 * @return Locality
	 */
	public function setTerritory($territory)
	{
		$this->territory = $territory;

		return $this;
	}

	/**
	 * Get postCode
	 *
	 * @return string
	 */
	public function getPostCode()
	{
		return $this->postCode;
	}

	/**
	 * Set postCode
	 *
	 * @param string $postCode
	 *
	 * @return Locality
	 */
	public function setPostCode($postCode)
	{
		$this->postCode = $postCode;

		return $this;
	}

	/**
	 * Get country
	 *
	 * @return string
	 */
	public function getCountry()
	{
		return $this->country;
	}

	/**
	 * Set country
	 *
	 * @param string $country
	 *
	 * @return Locality
	 */
	public function setCountry($country)
	{
		$this->country = $country;

		return $this;
	}
}
