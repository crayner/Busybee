<?php
namespace App\Address\Entity;

use Hillrange\Security\Util\UserTrackInterface;
use Hillrange\Security\Util\UserTrackTrait;
use Symfony\Component\Intl\Intl;

/**
 * Locality Model
 *
 * @version    31st October 2016
 * @since      31st October 2016
 * @author     Craig Rayner
 */
abstract class LocalityExtension implements UserTrackInterface
{
	use UserTrackTrait;
	/**
	 * @var    string
	 */
	protected $classSuffix = '';

	/**
	 * get classSuffix
	 *
	 * @version    31st October 2016
	 * @since      31st October 2016
	 * @author     Craig Rayner
	 */
	public function getClassSuffix()
	{
		return $this->classSuffix;
	}

	/**
	 * set classSuffix
	 *
	 * @version    31st October 2016
	 * @since      31st October 2016
	 * @author     Craig Rayner
	 */
	public function setClassSuffix($classSuffix)
	{
		$this->classSuffix = $classSuffix;

		return $this;
	}

	/**
	 * to String
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->getFullLocality();
	}

	/**
	 * get Full Locality
	 *
	 * @return string
	 */
	public function getFullLocality()
	{
		return str_replace('  ', ' ', trim($this->getName() . ' ' . $this->getTerritory() . ' ' . $this->getPostCode() . ' ' . $this->getCountryName()));
	}

	/**
	 * get Country Name
	 *
	 * @return string
	 */
	public function getCountryName()
	{
		return Intl::getRegionBundle()->getCountryName(strtoupper($this->getCountry()));
	}

	/**
	 * @return bool
	 */
	public function isEmpty(): bool
	{
		if (empty(trim($this->__toString())))
			return true;

		return false;
	}

}