<?php
namespace App\Entity;

use Hillrange\Security\Entity\User;
use Hillrange\Security\Util\UserTrackInterface;
use Hillrange\Security\Util\UserTrackTrait;

/**
 * Campus
 */
class Campus implements UserTrackInterface
{
	use UserTrackTrait;

	/**
	 * @var integer
	 */
	private $id;

	/**
	 * @var string
	 */
	private $identifier;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $postcode;

	/**
	 * @var string
	 */
	private $territory;

	/**
	 * @var string
	 */
	private $locality;

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
	 * Get identifier
	 *
	 * @return string
	 */
	public function getIdentifier()
	{
		return $this->identifier;
	}

	/**
	 * Set identifier
	 *
	 * @param string $identifier
	 *
	 * @return Campus
	 */
	public function setIdentifier($identifier)
	{
		$this->identifier = $identifier;

		return $this;
	}

	/**
	 * Get name
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Set name
	 *
	 * @param string $name
	 *
	 * @return Campus
	 */
	public function setName($name)
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * Get postcode
	 *
	 * @return string
	 */
	public function getPostcode()
	{
		return $this->postcode;
	}

	/**
	 * Set postcode
	 *
	 * @param string $postcode
	 *
	 * @return Campus
	 */
	public function setPostcode($postcode)
	{
		$this->postcode = $postcode;

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
	 * @return Campus
	 */
	public function setTerritory($territory)
	{
		$this->territory = $territory;

		return $this;
	}

	/**
	 * Get locality
	 *
	 * @return string
	 */
	public function getLocality()
	{
		return $this->locality;
	}

	/**
	 * Set locality
	 *
	 * @param string $locality
	 *
	 * @return Campus
	 */
	public function setLocality($locality)
	{
		$this->locality = $locality;

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
	 * @return Campus
	 */
	public function setCountry($country)
	{
		$this->country = $country;

		return $this;
	}
}
