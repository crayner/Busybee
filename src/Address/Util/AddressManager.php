<?php
namespace App\Address\Util;

use App\Core\Manager\SettingManager;
use App\Entity\Address;

class AddressManager
{
	private $settingManager;
	public function __construct(SettingManager $settingManager)
	{
		$this->settingManager = $settingManager;
	}

	/**
	 * Format Address
	 *
	 * @version    8th November 2016
	 * @since      8th November 2016
	 *
	 * @param    Address $address
	 *
	 * @return    html string
	 */
	public function formatAddress($address)
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

		return $this->settingManager->get('Address.Format', null, $data);
	}

	/**
	 * get Address List Label
	 *
	 * @version    8th November 2016
	 * @since      8th November 2016
	 *
	 * @param    Address $address
	 *
	 * @return    html string
	 */
	public function getAddressListLabel($address)
	{
		if ($address instanceof Address)
			$data = ['propertyName' => $address->getPropertyName(), 'streetName' => $address->getStreetName(),
			         'buildingType' => $address->getBuildingType(), 'buildingNumber' => $address->getBuildingNumber(),
			         'streetNumber' => $address->getStreetNumber(), 'locality' => $address->getLocality()->getName()];
		else
			$data = ['propertyName'   => null, 'streetName' => null, 'buildingType' => null,
			         'buildingNumber' => null, 'streetNumber' => null, 'locality' => null];

		return trim($this->settingManager->get('Address.ListLabel', null, $data));
	}
}