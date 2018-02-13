<?php
namespace App\Entity;

use App\People\Entity\PersonExtension;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Person
 */
class Person extends PersonExtension
{
	/**
	 * @var integer
	 */
	private $id;

	/**
	 * @var string
	 */
	private $honorific;

	/**
	 * @var string
	 */
	private $surname;

	/**
	 * @var string
	 */
	private $firstName;

	/**
	 * @var string
	 */
	private $preferredName;

	/**
	 * @var string
	 */
	private $officialName;

	/**
	 * @var string
	 */
	private $gender;

	/**
	 * @var \DateTime
	 */
	private $dob;

	/**
	 * @var string
	 */
	private $email;

	/**
	 * @var string
	 */
	private $email2;

	/**
	 * @var string
	 */
	private $website;

	/**
	 * @var string
	 */
	private $photo;

	/**
	 * @var null|UserInterface
	 */
	private $user;

	/**
	 * @var Address
	 */
	private $address1;

	/**
	 * @var Address
	 */
	private $address2;

	/**
	 * @var Collection
	 */
	private $phone;
	/**
	 * @var string
	 */
	private $identifier;

	/**
	 * @var string
	 */
	private $importIdentifier;

	/**
	 * @var string
	 */
	private $vehicleRegistration;

	/**
	 * @var ArrayCollection
	 */
	private $careGivers;

	/**
	 * @var string
	 */
	private $nameInCharacters;

	/**
	 * @var string
	 */
	private $comment;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->phone      = new ArrayCollection();
		$this->careGivers = new ArrayCollection();
		parent::__construct();
	}

	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId(): int
	{
		return is_null($this->id) ? 0 : $this->id;
	}


	/**
	 * @param int $id
	 */
	public function setId(int $id)
	{
		$this->id = $id;

		return $this;
	}

	/**
	 * Get title
	 *
	 * @return string
	 */
	public function getHonorific()
	{
		return $this->honorific;
	}

	/**
	 * Set title
	 *
	 * @param string $title
	 *
	 * @return Person
	 */
	public function setHonorific($honorific)
	{
		$this->honorific = $honorific;

		return $this;
	}

	/**
	 * Get surname
	 *
	 * @return string
	 */
	public function getSurname()
	{
		return $this->surname;
	}

	/**
	 * Set surname
	 *
	 * @param string $surname
	 *
	 * @return Person
	 */
	public function setSurname($surname)
	{
		$this->surname = $surname;

		return $this;
	}

	/**
	 * Get firstName
	 *
	 * @return string
	 */
	public function getFirstName()
	{
		return $this->firstName;
	}

	/**
	 * Set firstName
	 *
	 * @param string $firstName
	 *
	 * @return Person
	 */
	public function setFirstName($firstName)
	{
		$this->firstName = $firstName;

		return $this;
	}

	/**
	 * Get preferredName
	 *
	 * @return string
	 */
	public function getPreferredName()
	{
		return $this->preferredName;
	}

	/**
	 * Set preferredName
	 *
	 * @param string $preferredName
	 *
	 * @return Person
	 */
	public function setPreferredName($preferredName)
	{
		$this->preferredName = $preferredName;

		return $this;
	}

	/**
	 * Get officialName
	 *
	 * @return string
	 */
	public function getOfficialName()
	{
		return $this->officialName;
	}

	/**
	 * Set officialName
	 *
	 * @param string $officialName
	 *
	 * @return Person
	 */
	public function setOfficialName($officialName)
	{
		$this->officialName = $officialName;

		return $this;
	}

	/**
	 * Get gender
	 *
	 * @return string
	 */
	public function getGender()
	{
		return $this->gender;
	}

	/**
	 * Set gender
	 *
	 * @param string $gender
	 *
	 * @return Person
	 */
	public function setGender($gender)
	{
		$this->gender = $gender;

		return $this;
	}

	/**
	 * Get dob
	 *
	 * @return \DateTime
	 */
	public function getDob()
	{
		return $this->dob;
	}

	/**
	 * Set dob
	 *
	 * @param \DateTime $dob
	 *
	 * @return Person
	 */
	public function setDob($dob)
	{
		$this->dob = $dob;

		return $this;
	}

	/**
	 * Get email
	 *
	 * @return string
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * Set email
	 *
	 * @param string $email
	 *
	 * @return Person
	 */
	public function setEmail($email)
	{
		$this->email = $email;

		return $this;
	}

	/**
	 * Get email2
	 *
	 * @return string
	 */
	public function getEmail2()
	{
		return $this->email2;
	}

	/**
	 * Set email2
	 *
	 * @param string $email2
	 *
	 * @return Person
	 */
	public function setEmail2($email2)
	{
		$this->email2 = $email2;

		return $this;
	}

	/**
	 * Get website
	 *
	 * @return string
	 */
	public function getWebsite()
	{
		return $this->website;
	}

	/**
	 * Set website
	 *
	 * @param string $website
	 *
	 * @return Person
	 */
	public function setWebsite($website)
	{
		$this->website = $website;

		return $this;
	}

	/**
	 * Get photo
	 *
	 * @return string
	 */
	public function getPhoto()
	{
		return $this->photo;
	}

	/**
	 * Set photo
	 *
	 * @param string $photo
	 *
	 * @return Person
	 */
	public function setPhoto($photo)
	{
		$this->photo = $photo;

		return $this;
	}

	/**
	 * Get user
	 *
	 * @return null|UserInterface
	 */
	public function getUser(): ?UserInterface
	{
		return $this->user;
	}

	/**
	 * Set user
	 *
	 * @param null|UserInterface $user
	 *
	 * @return Person
	 */
	public function setUser(UserInterface $user = null): Person
	{
		$this->user = $user;

		return $this;
	}

	/**
	 * Get address1
	 *
	 * @return Address
	 */
	public function getAddress1()
	{
		return $this->address1;
	}

	/**
	 * Set address1
	 *
	 * @param Address $address1
	 *
	 * @return Person
	 */
	public function setAddress1(Address $address1 = null)
	{
		$this->address1 = $address1;

		return $this;
	}

	/**
	 * Get address2
	 *
	 * @return Address
	 */
	public function getAddress2()
	{
		return $this->address2;
	}

	/**
	 * Set address2
	 *
	 * @param Address $address2
	 *
	 * @return Person
	 */
	public function setAddress2(Address $address2 = null)
	{
		$this->address2 = $address2;

		return $this;
	}

	/**
	 * Add phone
	 *
	 * @param Phone $phone
	 *
	 * @return Person
	 */
	public function addPhone(Phone $phone)
	{
		$this->phone[] = $phone;

		return $this;
	}

	/**
	 * Remove phone
	 *
	 * @param Phone $phone
	 */
	public function removePhone(Phone $phone)
	{
		$this->phone->removeElement($phone);
	}

	/**
	 * Get phone
	 *
	 * @return Collection
	 */
	public function getPhone()
	{
		return $this->phone;
	}

	/**
	 * Get identifier
	 *
	 * @return string
	 */
	public function getIdentifier()
	{
        $this->identifier = $this->createIdentifier($this->identifier);
		if (empty($this->identifier) || false !== mb_strpos($this->identifier, '*'))
			$this->setIdentifier('');

		return strtoupper($this->identifier);
	}

	/**
	 * Set identifier
	 *
	 * @param string $identifier
	 *
	 * @return Person
	 */
	public function setIdentifier($identifier)
	{

		$this->identifier = strtoupper($this->createIdentifier($identifier));

		return $this;
	}

	private function createIdentifier($identifier)
    {
        if ((empty($identifier) || false !== mb_strpos($identifier, '*')) && (empty($this->identifier) || false !== mb_strpos($this->identifier, '*')))
        {
            $identifier = mb_substr($this->surname, 0, 4);
            while (mb_strlen($identifier) < 4)
                $identifier .= '_';
            $given = explode(' ', $this->firstName);
            while(count($given) > 2)
                array_pop($given);
            foreach($given as $name)
                $identifier .= mb_substr($name, 0, 1);
            while (mb_strlen($identifier) < 6)
                $identifier .= '*';
            $mon = '**';
            if ($this->getDob() instanceof \DateTime)
                $mon = $this->getDob()->format('m');
            $identifier .= $mon;
            $mon = '**';
            if ($this->getDob() instanceof \DateTime)
                $mon = $this->getDob()->format('d');
            $identifier .= $mon;

            while (mb_strlen($identifier) < 10)
                $identifier .= '*';
        }

        return $identifier;
    }
	/**
	 * Get importIdentifier
	 *
	 * @return string
	 */
	public function getImportIdentifier(): ?string
	{
		return $this->importIdentifier;
	}

	/**
	 * Set importIdentifier
	 *
	 * @param string $importIdentifier
	 *
	 * @return Person
	 */
	public function setImportIdentifier($importIdentifier): Person
	{
		$this->importIdentifier = $importIdentifier;

		return $this;
	}

	/**
	 * Get vehicleRegistration
	 *
	 * @return string|null
	 */
	public function getVehicleRegistration(): ?string
	{
		return $this->vehicleRegistration;
	}

	/**
	 * Set vehicleRegistration
	 *
	 * @param string $vehicleRegistration
	 *
	 * @return Person
	 */
	public function setVehicleRegistration($vehicleRegistration): Person
	{
		$this->vehicleRegistration = $vehicleRegistration;

		return $this;
	}

	/**
	 * Set Phone
	 *
	 * @param Collection $phone
	 *
	 * @return $this
	 */
	public function setPhone(Collection $phone): Person
	{
		$this->phone = $phone;

		return $this;
	}

	/**
	 * Get Care Givers
	 *
	 * @return ArrayCollection
	 */
	public function getCareGivers(): ArrayCollection
	{
		return $this->careGivers;
	}

	/**
	 * Set Care Givers
	 *
	 * @param ArrayCollection $careGivers
	 *
	 * @return Person
	 */
	public function setCareGivers(ArrayCollection $careGivers): Person
	{
		$this->careGivers = $careGivers;

		return $this;
	}

	/**
	 * Add Care Giver
	 *
	 * @param CareGiver|null $careGiver
	 *
	 * @return Person
	 */
	public function addCareGiver(CareGiver $careGiver = null): Person
	{
		if (!$careGiver instanceof CareGiver)
			return $this;

		if (!$this->careGivers->contains($careGiver))
			$this->careGivers->add($careGiver);

		return $this;
	}

	/**
	 * Remove Care Giver
	 *
	 * @param CareGiver $careGiver
	 *
	 * @return Person
	 */
	public function removeCareGiver(CareGiver $careGiver): Person
	{
		$this->careGivers->removeElement($careGiver);

		return $this;
	}

	/**
	 * @return string
	 */
	public function getNameInCharacters(): ?string
	{
		return $this->nameInCharacters;
	}

	/**
	 * @param string $nameInCharacters
	 */
	public function setNameInCharacters(string $nameInCharacters): Person
	{
		$this->nameInCharacters = $nameInCharacters;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getComment(): ?string
	{
		return $this->comment;
	}

	/**
	 * @param string $comment
	 *
	 * @return Person
	 */
	public function setComment(string $comment): Person
	{
		$this->comment = $comment;

		return $this;
	}
}
