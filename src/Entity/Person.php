<?php
namespace App\Entity;

use App\People\Entity\PersonExtension;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Yaml\Yaml;

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
	private $phones;
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
		$this->phones      = new ArrayCollection();
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
	public function setId(int $id): Person
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
     * @return Collection
     */
    public function getPhones(): Collection
    {
        if (empty($this->phones))
            $this->phones = new ArrayCollection();

        if($this->phones instanceof PersistentCollection)
            $this->phones->initialize();

        return $this->phones;
    }

    /**
     * @param Collection|null $phones
     * @return Family
     */
    public function setPhones(?Collection $phones): Person
    {
        $this->phones = $phones;
        return $this;
    }

    /**
     * Add phone
     *
     * @param Phone $phone
     *
     * @return Family
     */
    public function addPhone(?Phone $phone): Person
    {
        if (empty($this->phones) || $this->getPhones()->contains($phone))
            return $this;

        $this->phones->add($phone);

        return $this;
    }

    /**
     * Remove phone
     *
     * @param Phone $phone
     */
    public function removePhone(?Phone $phone): Person
    {
        $this->getPhones()->removeElement($phone);

        return $this;
    }

	/**
	 * Get identifier
	 *
	 * @return string
	 */
	public function getIdentifier()
	{
        $this->identifier = $this->createStudentIdentifier($this->identifier);

        $this->identifier = strtoupper($this->generateIdentifier());

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

		$this->identifier = strtoupper($this->createStudentIdentifier($identifier));

		$this->identifier = strtoupper($this->generateIdentifier());

		return $this;
	}

    /**
     * @param $identifier
     * @return string
     */
    private function createStudentIdentifier($identifier)
    {
        if (! empty($identifier))
            $this->identifier = $identifier;

        if (!$this instanceof Student)
            return $this->identifier;

        if (empty($this->getDob()))
            return $this->identifier;

        if (empty($this->identifier) || mb_strpos($this->identifier, '*') || mb_substr($this->identifier, 1, 1) === '_')
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
            $mon = '****';
            if ($this->getDob() instanceof \DateTime)
                $mon = $this->getDob()->format('md');
            $identifier .= $mon;

            while (mb_strlen($identifier) < 10)
                $identifier .= '*';
        }

        $this->identifier = $identifier;

        return $identifier;
    }

    /**
     * @return string
     */
    private function generateIdentifier(): string
    {
        if (! empty($this->identifier))
            return $this->identifier;

        $index = $this->getIdentifierIndex();

        return mb_substr($this->getSurname(), 0, 1) . '_' . $index ;
    }

    /**
     * @return string
     */
    private function incrementIndex($index): string
    {
        $index = mb_substr($index, 0, 3) . chr(ord(mb_substr($index,3)) + 1);
        if (mb_substr($index,3) === '[')
        {
            $index = mb_substr($index, 0, 2) . chr(ord(mb_substr($index, 2, 1)) + 1) . 'A';
            if (mb_substr($index,2,1) === '[')
            {
                $index = str_replace('[', 'A', $index);
                $index = str_pad(strval(intval($index) + 1), 2, '0', STR_PAD_LEFT) . mb_substr($index, -2);
            }
        }
        return $index;
    }

    /**
     * @return string
     */
    private function getIdentifierIndex()
    {
        // Read index in busybee.yaml
        $file = realpath('../config/packages/busybee.yaml');

        $params = Yaml::parse(file_get_contents( $file));
        if (isset($params['parameters']['person_identifier_index']))
        {
            $index = $params['parameters']['person_identifier_index'];
            if (mb_substr($index, 0, 4) !== date('ym'))
                $index = '00AA';
            else
                $index = $this->incrementIndex(mb_substr($index, 4));
        } else
            $index = '00AA';

        $index = date('ym') . $index;

        $params['parameters']['person_identifier_index'] = $index;

        // Write index in busybee.yaml
        file_put_contents( $file, Yaml::dump($params));

        return $index;
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
