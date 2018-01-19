<?php
namespace App\Entity;

use App\Address\Entity\PhoneExtension;

/**
 * Phone
 */
class Phone extends PhoneExtension
{
	/**
	 * @var integer
	 */
	private $id;

	/**
	 * @var string
	 */
	private $phoneType;

	/**
	 * @var string
	 */
	private $phoneNumber;

	/**
	 * @var string
	 */
	private $countryCode;

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
	 * Get phoneType
	 *
	 * @return string
	 */
	public function getPhoneType()
	{
		return $this->phoneType;
	}

	/**
	 * Set phoneType
	 *
	 * @param string $phoneType
	 *
	 * @return Phone
	 */
	public function setPhoneType($phoneType)
	{
		$this->phoneType = $phoneType;

		return $this;
	}

	/**
	 * Get phoneNumber
	 *
	 * @return string
	 */
	public function getPhoneNumber()
	{
		return $this->phoneNumber;
	}

	/**
	 * Set phoneNumber
	 *
	 * @param string $phoneNumber
	 *
	 * @return Phone
	 */
	public function setPhoneNumber($phoneNumber)
	{
		$this->phoneNumber = $phoneNumber;

		return $this;
	}

	/**
	 * Get countryCode
	 *
	 * @return string
	 */
	public function getCountryCode()
	{
		return $this->countryCode;
	}

	/**
	 * Set countryCode
	 *
	 * @param string $countryCode
	 *
	 * @return Phone
	 */
	public function setCountryCode($countryCode)
	{
		$this->countryCode = $countryCode;

		return $this;
	}
}
