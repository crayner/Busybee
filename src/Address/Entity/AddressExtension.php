<?php
namespace App\Address\Entity;

use App\Entity\Locality;
use Hillrange\Security\Util\UserTrackInterface;
use Hillrange\Security\Util\UserTrackTrait;


/**
 * Address Model
 *
 * @version    8th November 2016
 * @since      31st October 2016
 * @author     Craig Rayner
 */
abstract class AddressExtension implements UserTrackInterface
{
	use UserTrackTrait;
	/**
	 * @var    array
	 */
	protected $buildingTypeList;

	/**
	 * @var    array
	 */
	protected $address1_list = array();

	/**
	 * @var    array
	 */
	protected $address2_list = array();

	/**
	 * Construct
	 *
	 * @return AddressExtension
	 */
	public function __construct()
	{
		$this->setLocality(new Locality());

		return $this;
	}

	/**
	 * get BuildingType List
	 *
	 * @return array
	 */
	public function getBuildingTypeList()
	{
		return $this->buildingTypeList;
	}

	/**
	 * set BuildingType List
	 *
	 * @param    array $list
	 *
	 * @return AddressExtension
	 */
	public function setBuildingTypeList($list)
	{
		$this->buildingTypeList = $list;

		return $this;
	}

	/**
	 * get BuildingType List
	 *
	 * @return string
	 */
	public function getSingleLineAddress()
	{
		return trim($this->getStreetNumber() . ' ' . $this->getStreetName() . ' ' . $this->getLocality()->getName());
	}

	/**
	 * to String
	 *
	 * @return mixed
	 */
	public function __toString(): string
	{
		return $this->getSingleLineAddress();
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