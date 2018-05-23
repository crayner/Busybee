<?php
namespace App\Entity;

use App\People\Entity\FamilyExtension;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;

/**
 * Family
 */
class Family extends FamilyExtension
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
	 * @var Address
	 */
	private $address1;

	/**
	 * @var Address
	 */
	private $address2;

	/**
	 * @var \Doctrine\Common\Collections\Collection
	 */
	private $phones;

	/**
	 * @var \Doctrine\Common\Collections\Collection
	 */
	private $students;
	/**
	 * @var \Doctrine\Common\Collections\Collection
	 */
	private $careGivers;

	/**
	 * @var string
	 */
	private $firstLanguage;

	/**
	 * @var string
	 */
	private $secondLanguage;
	/**
	 * @var string
	 */
	private $house;

	/**
	 * @var string
	 */
	private $importIdentifier;

	/**
	 * @var string
	 */
	private $status;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->phone      = new ArrayCollection();
		$this->students   = new ArrayCollection();
		$this->careGivers = new ArrayCollection();
	}

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
	 * Get name
	 *
	 * @return string
	 */
	public function getName(): ?string
	{
		return $this->name;
	}

	/**
	 * Set name
	 *
	 * @param string $name
	 *
	 * @return Family
	 */
	public function setName($name): Family
	{
		$this->name = $name;

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
	 * @return Family
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
	 * @return Family
	 */
	public function setAddress2(Address $address2 = null)
	{
		$this->address2 = $address2;

		return $this;
	}

	/**
	 * Add student
	 *
	 * @param  Student $student
	 *
	 * @return Family
	 */
	public function addStudent(?Student $student): Family
	{
	    if (empty($student) || $this->getStudents()->contains($student))
	        return $this;

		$this->students->add($student);

		return $this;
	}

	/**
	 * Remove student
	 *
	 * @param Student $student
	 */
	public function removeStudent(Student $student)
	{
		$this->students->removeElement($student);
	}

	/**
	 * Get Students
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getStudents(): Collection
	{
	    if (empty($this->students))
            $this->students = new ArrayCollection();

	    if ($this->students instanceof PersistentCollection)
	        $this->students->initialize();

		return $this->students;
	}

	/**
	 * Get Students
	 *
	 * @param \Doctrine\Common\Collections\Collection
	 *
	 * @return Family
	 */
	public function setStudents(ArrayCollection $students)
	{
		$this->students = $students;

		return $this;
	}

	/**
	 * Add careGiver
	 *
	 * @param CareGiver $careGiver
	 *
	 * @return Family
	 */
	public function addCareGiver(CareGiver $careGiver)
	{
		if (empty($careGiver->getFamily()))
			$careGiver->setFamily($this);

		$this->careGivers->add($careGiver);

		return $this;
	}

	/**
	 * Remove careGiver
	 *
	 * @param CareGiver $careGiver
	 */
	public function removeCareGiver(CareGiver $careGiver)
	{
		$this->careGivers->removeElement($careGiver);
	}

	/**
	 * Get careGiver
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getCareGivers()
	{
		return $this->careGivers;
	}

	/**
	 * Set careGiver
	 *
	 * @param   ArrayCollection
	 *
	 * @return Family
	 */
	public function setCareGivers(ArrayCollection $caregivers)
	{
		$this->careGivers = $caregivers;

		return $this;
	}

	/**
	 * Get firstLanguage
	 *
	 * @return string
	 */
	public function getFirstLanguage()
	{
		return $this->firstLanguage;
	}

	/**
	 * Set firstLanguage
	 *
	 * @param string $firstLanguage
	 *
	 * @return Family
	 */
	public function setFirstLanguage($firstLanguage)
	{
		$this->firstLanguage = $firstLanguage;

		return $this;
	}

	/**
	 * Get secondLanguage
	 *
	 * @return string
	 */
	public function getSecondLanguage()
	{
		return $this->secondLanguage;
	}

	/**
	 * Set secondLanguage
	 *
	 * @param string $secondLanguage
	 *
	 * @return Family
	 */
	public function setSecondLanguage($secondLanguage)
	{
		$this->secondLanguage = $secondLanguage;

		return $this;
	}

	/**
	 * Get house
	 *
	 * @return string
	 */
	public function getHouse()
	{
		return strtolower($this->house);
	}

	/**
	 * Set house
	 *
	 * @param string $house
	 *
	 * @return Family
	 */
	public function setHouse($house)
	{
		$this->house = $house;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getImportIdentifier(): ?string
	{
		return $this->importIdentifier;
	}

	/**
	 * @param string $importIdentifier
	 *
	 * @return Family
	 */
	public function setImportIdentifier(string $importIdentifier): Family
	{
		$this->importIdentifier = $importIdentifier;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getStatus(): ?string
	{
		return $this->status;
	}

	/**
	 * @param string $status
	 *
	 * @return Family
	 */
	public function setStatus(string $status): Family
	{
		$this->status = $status;

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
     * @param Collection $phones
     * @return Family
     */
    public function setPhones(Collection $phones): Family
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
    public function addPhone(?Phone $phone): Family
    {
        if (empty($this->phones) || $this->getPhones()->contains($phone))
            return $this;

        $this->phones->add($phone);

        return $this;
    }

    /**
     * Remove phone
     * @param Phone|null $phone
     * @return Family
     */
    public function removePhone(?Phone $phone): Family
    {
        $this->getPhones()->removeElement($phone);

        return $this;
    }
}
