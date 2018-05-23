<?php
namespace App\School\Util;

use App\Core\Manager\MessageManager;
use App\Core\Manager\SettingManager;
use App\Entity\Family;
use App\Entity\Staff;
use App\Entity\Student;
use App\School\Entity\House;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Yaml\Yaml;

class HouseManager
{
	/**
	 * @var ArrayCollection
	 */
	private $houses;

	/**
	 * @var SettingManager
	 */
	private $settingManager;

	/**
	 * @var EntityManagerInterface
	 */
	private $objectManager;

	/**
	 * @var MessageManager
	 */
	private $messages;

	/**
	 * HouseManager constructor.
	 *
	 * @param SettingManager         $settingManager
	 * @param EntityManagerInterface $objectManager
	 */
	public function __construct(SettingManager $settingManager, EntityManagerInterface $objectManager, MessageManager $messages)
	{
		$this->houses         = new ArrayCollection();
		$this->settingManager = $settingManager;
		$this->objectManager  = $objectManager;
		$this->messages       = $messages->setDomain('School');
		$this->loadHouses();
	}

	/**
	 * @return HouseManager
	 */
	private function loadHouses(): HouseManager
	{
		$houses = $this->settingManager->get('house.list', []);

		foreach ($houses as $w)
		{
			if (is_array($w))
			{
				$house = new House();
				$house->setName($w['name']);
				$house->setShortName($w['shortName']);
				$house->setLogo($w['logo']);
				$this->addHouse($house);
			} else {
				$house = new House();
				$house->setName($w);
				$house->setShortName(mb_substr($w, 0, 2));
				$house->setLogo(null);
				$this->addHouse($house);
			}
		}

		return $this;
	}

	/**
	 * @param House $house
	 *
	 * @return HouseManager
	 */
	public function addHouse(House $house): HouseManager
	{
		if ($this->houses->contains($house))
			return $this;

		$this->houses->add($house);

		return $this;
	}

	/**
	 * @return ArrayCollection
	 */
	public function getHouses(): ArrayCollection
	{
		return $this->houses;
	}

	/**
	 * @param ArrayCollection $houses
	 *
	 * @return HouseManager
	 */
	public function setHouses(ArrayCollection $houses): HouseManager
	{
		$this->houses = $houses;

		return $this;
	}

	/**
	 * @param House $house
	 *
	 * @return HouseManager
	 */
	public function removeHouse(House $house): HouseManager
	{
		$this->houses->removeElement($house);

		return $this;
	}

    /**
     * getStatus
     *
     * @param House $house
     * @return int
     * @throws \Doctrine\ORM\ORMException
     */
    public function getStatus(House $house): int
	{
		$x = 0;
		$x = $x + count($this->objectManager->getRepository(Student::class)->findByHouse($house->getName()));
        $x = $x + count($this->objectManager->getRepository(Staff::class)->findByHouse($house->getName()));
        $x = $x + count($this->objectManager->getRepository(Family::class)->findByHouse($house->getName()));
		$house->setStatus($x);

		return $house->getStatus();
	}

	/**
	 * @param Form $form
	 *
	 * @return mixed
	 */
	public function saveHouses(Form $form = null)
	{
		if (is_null($form))
			$data = $this->houses;
		else
			$data = $form->get('houses')->getData();

		$houses = [];

		$iterator = $data->getIterator();
		$iterator->uasort(function ($a, $b) {
			return ($a->getName() < $b->getName()) ? -1 : 1;
		});
		$data = new ArrayCollection(iterator_to_array($iterator, false));

        $hl = [];

		foreach ($data->toArray() as $house)
		{
			$w                  = [];
			$w['name']          = $house->getName();
			$w['shortName']     = $house->getShortName();
			$w['logo']          = $house->getLogo();
			$houses[strtolower($w['name'])] = $w;
			$hl[strtolower($w['name'])] = $w['name'];
		}

		$this->houses = $data;

		if (file_exists(__DIR__.'/../../../config/translations/Setting.en.yaml'))
            $data = Yaml::parse(file_get_contents(__DIR__.'/../../../config/translations/Setting.en.yaml'));
        else
            $data = [];

        $data['house.list'] = $hl;

        file_put_contents(__DIR__.'/../../../config/translations/Setting.en.yaml', Yaml::dump($data));
		return $this->getSettingManager()->set('house.list', $houses);
	}

	/**
	 * @return SettingManager
	 */
	public function getSettingManager(): SettingManager
	{
		return $this->settingManager;
	}

	/**
	 * @param string $houseName
	 */
	public function deleteLogo(string $houseName)
	{
		$house = $this->findHouse($houseName);

		$file = $house->getLogo();

		if (file_exists($file))
		{
			$this->removeHouse($house);
			if (unlink($file))
				$house->getLogo(null);
			$this->addHouse($house);

			$this->saveHouses();
		}
	}

	/**
	 * @param $houseName
	 *
	 * @return mixed
	 */
	public function findHouse($houseName)
	{
		foreach ($this->houses->toArray() as $q => $w)
		{
			if ($houseName == $w->getName())
			{
				return $w;
			}
		}
	}

	/**
	 * @return MessageManager
	 */
	public function getMessages(): MessageManager
	{
		return $this->messages;
	}
}