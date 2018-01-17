<?php
namespace App\Repository;

use App\Entity\Locality;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Intl\Intl;

/**
 * LocalityRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class LocalityRepository extends EntityRepository
{
	/**
	 * get Locality Choices
	 *
	 * @version    28th October 2016
	 * @since      28th October 2016
	 * @return    array
	 */
	public function getLocalityChoices()
	{
		$x      = $this->findBy(array(), array('locality' => 'ASC', 'territory' => 'ASC', 'postCode' => 'ASC'));
		$result = array();
		foreach ($x as $w)
			$result[$w->getLocality() . ' ' . $w->getTerritory() . ' ' . $w->getpostCode() . ' ' . Intl::getRegionBundle()->getCountryName(strtoupper($w->getCountry()))] = $w->getId();

		return $result;
	}

	/**
	 * set Address Locality
	 *
	 * @version    28th October 2016
	 * @since      28th October 2016
	 *
	 * @param    integer $id
	 *
	 * @return    array
	 */
	public function setAddressLocality($id)
	{
		if ($id instanceof Locality)
		{
			$id->injectRepository($this);

			return $id;
		}
		if (intval($id) > 0)
			$entity = $this->findOneById($id);
		else
			$entity = new Locality();
		$entity->injectRepository($this);

		return $entity;
	}
}
