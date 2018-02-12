<?php
namespace App\Entity;

use App\People\Entity\StudentExtension;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Student
 */
class Student extends StudentExtension
{
	/**
	 * @var \DateTime
	 */
	private $startAtSchool;

	/**
	 * @var \DateTime
	 */
	private $startAtThisSchool;

	/**
	 * @var \DateTime
	 */
	private $lastAtThisSchool;

	/**
	 * @var string
	 */
	private $locker;

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
	private $thirdLanguage;

	/**
	 * @var string
	 */
	private $countryOfBirth;

	/**
	 * @var string
	 */
	private $birthCertificateScan;

	/**
	 * @var string
	 */
	private $ethnicity;

	/**
	 * @var string
	 */
	private $citizenship1;

	/**
	 * @var string
	 */
	private $citizenship1Passport;

	/**
	 * @var string
	 */
	private $citizenship1PassportScan;

	/**
	 * @var string
	 */
	private $citizenship2;

	/**
	 * @var string
	 */
	private $citizenship2Passport;

	/**
	 * @var string
	 */
	private $religion;

	/**
	 * @var string
	 */
	private $nationalIDCardNumber;

	/**
	 * @var string
	 */
	private $nationalIDCardScan;

	/**
	 * @var string
	 */
	private $residencyStatus;

	/**
	 * @var \DateTime
	 */
	private $visaExpiryDate;

	/**
	 * @var string
	 */
	private $house;

	/*
	 * @var
	 */
	private $calendarGroups;

	/**
	 * @var string
	 */
	private $status;

	/**
	 * @var string
	 */
	private $lastSchool;

	/**
	 * @var string
	 */
	private $nextSchool;

	/**
	 * @var string
	 */
	private $departureReason;

	/**
	 * @var string
	 */
	private $transport;

	/**
	 * @var string
	 */
	private $transportNotes;

	/**
	 * @var string
	 */
	private $dayType;

    /**
     * @var ArrayCollection
     */
    private $rolls;

	/**
	 * Student constructor.
	 */
	public function __construct()
	{
		$this->calendarGroups = new ArrayCollection();
		$this->rolls = new ArrayCollection();
		parent::__construct();
	}

	/**
	 * Set startAtSchool
	 *
	 * @param \DateTime $startAtSchool
	 *
	 * @return Student
	 */
	public function setStartAtSchool($startAtSchool)
	{
		$this->startAtSchool = $startAtSchool;

		return $this;
	}

	/**
	 * Get startAtSchool
	 *
	 * @return \DateTime
	 */
	public function getStartAtSchool()
	{
		if (empty($this->startAtSchool))
			$this->setStartAtSchool(new \DateTime());


		return $this->startAtSchool;
	}

	/**
	 * Set startAtThisSchool
	 *
	 * @param \DateTime $startAtThisSchool
	 *
	 * @return Student
	 */
	public function setStartAtThisSchool($startAtThisSchool)
	{
		$this->startAtThisSchool = $startAtThisSchool;

		return $this;
	}

	/**
	 * Get startAtThisSchool
	 *
	 * @return \DateTime
	 */
	public function getStartAtThisSchool()
	{
		if (empty($this->startAtThisSchool))
			$this->setStartAtThisSchool(new \DateTime());

		return $this->startAtThisSchool;
	}

	/**
	 * Set lastAtThisSchool
	 *
	 * @param \DateTime $lastAtThisSchool
	 *
	 * @return Student
	 */
	public function setLastAtThisSchool($lastAtThisSchool)
	{
		$this->lastAtThisSchool = $lastAtThisSchool;

		return $this;
	}

	/**
	 * Get lastAtThisSchool
	 *
	 * @return \DateTime
	 */
	public function getLastAtThisSchool()
	{
		return $this->lastAtThisSchool;
	}

	/**
	 * Set locker
	 *
	 * @param string $locker
	 *
	 * @return Student
	 */
	public function setLocker($locker)
	{
		$this->locker = $locker;

		return $this;
	}

	/**
	 * Get locker
	 *
	 * @return string
	 */
	public function getLocker()
	{
		return $this->locker;
	}

	/**
	 * Set firstLanguage
	 *
	 * @param string $firstLanguage
	 *
	 * @return Student
	 */
	public function setFirstLanguage($firstLanguage)
	{
		$this->firstLanguage = $firstLanguage;

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
	 * Set secondLanguage
	 *
	 * @param string $secondLanguage
	 *
	 * @return Student
	 */
	public function setSecondLanguage($secondLanguage)
	{
		$this->secondLanguage = $secondLanguage;

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
	 * Set thirdLanguage
	 *
	 * @param string $thirdLanguage
	 *
	 * @return Student
	 */
	public function setThirdLanguage($thirdLanguage)
	{
		$this->thirdLanguage = $thirdLanguage;

		return $this;
	}

	/**
	 * Get thirdLanguage
	 *
	 * @return string
	 */
	public function getThirdLanguage()
	{
		return $this->thirdLanguage;
	}

	/**
	 * Set countryOfBirth
	 *
	 * @param string $countryOfBirth
	 *
	 * @return Student
	 */
	public function setCountryOfBirth($countryOfBirth)
	{
		$this->countryOfBirth = $countryOfBirth;

		return $this;
	}

	/**
	 * Get countryOfBirth
	 *
	 * @return string
	 */
	public function getCountryOfBirth()
	{
		return $this->countryOfBirth;
	}

	/**
	 * Set birthCertificateScan
	 *
	 * @param string $birthCertificateScan
	 *
	 * @return Student
	 */
	public function setBirthCertificateScan($birthCertificateScan)
	{
		$this->birthCertificateScan = $birthCertificateScan;

		return $this;
	}

	/**
	 * Get birthCertificateScan
	 *
	 * @return string
	 */
	public function getBirthCertificateScan()
	{
		return $this->birthCertificateScan;
	}

	/**
	 * Set ethnicity
	 *
	 * @param string $ethnicity
	 *
	 * @return Student
	 */
	public function setEthnicity($ethnicity)
	{
		$this->ethnicity = $ethnicity;

		return $this;
	}

	/**
	 * Get ethnicity
	 *
	 * @return string
	 */
	public function getEthnicity()
	{
		return $this->ethnicity;
	}

	/**
	 * Set citizenship1
	 *
	 * @param string $citizenship1
	 *
	 * @return Student
	 */
	public function setCitizenship1($citizenship1)
	{
		$this->citizenship1 = $citizenship1;

		return $this;
	}

	/**
	 * Get citizenship1
	 *
	 * @return string
	 */
	public function getCitizenship1()
	{
		return $this->citizenship1;
	}

	/**
	 * Set citizenship1Passport
	 *
	 * @param string $citizenship1Passport
	 *
	 * @return Student
	 */
	public function setCitizenship1Passport($citizenship1Passport)
	{
		$this->citizenship1Passport = $citizenship1Passport;

		return $this;
	}

	/**
	 * Get citizenship1Passport
	 *
	 * @return string
	 */
	public function getCitizenship1Passport()
	{
		return $this->citizenship1Passport;
	}

	/**
	 * Set citizenship1PassportScan
	 *
	 * @param string $citizenship1PassportScan
	 *
	 * @return Student
	 */
	public function setCitizenship1PassportScan($citizenship1PassportScan)
	{
		$this->citizenship1PassportScan = $citizenship1PassportScan;

		return $this;
	}

	/**
	 * Get citizenship1PassportScan
	 *
	 * @return string
	 */
	public function getCitizenship1PassportScan()
	{
		return $this->citizenship1PassportScan;
	}

	/**
	 * Set citizenship2
	 *
	 * @param string $citizenship2
	 *
	 * @return Student
	 */
	public function setCitizenship2($citizenship2)
	{
		$this->citizenship2 = $citizenship2;

		return $this;
	}

	/**
	 * Get citizenship2
	 *
	 * @return string
	 */
	public function getCitizenship2()
	{
		return $this->citizenship2;
	}

	/**
	 * Set citizenship2Passport
	 *
	 * @param string $citizenship2Passport
	 *
	 * @return Student
	 */
	public function setCitizenship2Passport($citizenship2Passport)
	{
		$this->citizenship2Passport = $citizenship2Passport;

		return $this;
	}

	/**
	 * Get citizenship2Passport
	 *
	 * @return string
	 */
	public function getCitizenship2Passport()
	{
		return $this->citizenship2Passport;
	}

	/**
	 * Set religion
	 *
	 * @param string $religion
	 *
	 * @return Student
	 */
	public function setReligion($religion)
	{
		$this->religion = $religion;

		return $this;
	}

	/**
	 * Get religion
	 *
	 * @return string
	 */
	public function getReligion()
	{
		return $this->religion;
	}

	/**
	 * Set nationalIDCardNumber
	 *
	 * @param string $nationalIDCardNumber
	 *
	 * @return Student
	 */
	public function setNationalIDCardNumber($nationalIDCardNumber)
	{
		$this->nationalIDCardNumber = $nationalIDCardNumber;

		return $this;
	}

	/**
	 * Get nationalIDCardNumber
	 *
	 * @return string
	 */
	public function getNationalIDCardNumber()
	{
		return $this->nationalIDCardNumber;
	}

	/**
	 * Set nationalIDCardScan
	 *
	 * @param string $nationalIDCardScan
	 *
	 * @return Student
	 */
	public function setNationalIDCardScan($nationalIDCardScan)
	{
		$this->nationalIDCardScan = $nationalIDCardScan;

		return $this;
	}

	/**
	 * Get nationalIDCardScan
	 *
	 * @return string
	 */
	public function getNationalIDCardScan()
	{
		return $this->nationalIDCardScan;
	}

	/**
	 * Set residencyStatus
	 *
	 * @param string $residencyStatus
	 *
	 * @return Student
	 */
	public function setResidencyStatus($residencyStatus)
	{
		$this->residencyStatus = $residencyStatus;

		return $this;
	}

	/**
	 * Get residencyStatus
	 *
	 * @return string
	 */
	public function getResidencyStatus()
	{
		return $this->residencyStatus;
	}

	/**
	 * Set visaExpiryDate
	 *
	 * @param \DateTime $visaExpiryDate
	 *
	 * @return Student
	 */
	public function setVisaExpiryDate($visaExpiryDate)
	{
		$this->visaExpiryDate = $visaExpiryDate;

		return $this;
	}

	/**
	 * Get visaExpiryDate
	 *
	 * @return \DateTime
	 */
	public function getVisaExpiryDate()
	{
		return $this->visaExpiryDate;
	}

	/**
	 * Set house
	 *
	 * @param string $house
	 *
	 * @return Student
	 */
	public function setHouse($house)
	{
		$this->house = $house;

		return $this;
	}

	/**
	 * Get house
	 *
	 * @return string
	 */
	public function getHouse()
	{
		return $this->house;
	}

	/**
	 * @return mixed
	 */
	public function getCalendarGroups()
	{
		return $this->calendarGroups;
	}

	/**
	 * @param mixed $calendarGroups
	 */
	public function setCalendarGroups($calendarGroups): Student
	{
		$this->calendarGroups = $calendarGroups;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getStatus(): string
	{
		return $this->status;
	}

	/**
	 * @param string $status
	 */
	public function setStatus(string $status): Student
	{
		$this->status = $status;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getLastSchool(): string
	{
		return $this->lastSchool;
	}

	/**
	 * @param string $lastSchool
	 */
	public function setLastSchool(string $lastSchool): Student
	{
		$this->lastSchool = $lastSchool;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getNextSchool(): string
	{
		return $this->nextSchool;
	}

	/**
	 * @param string $nextSchool
	 */
	public function setNextSchool(string $nextSchool): Student
	{
		$this->nextSchool = $nextSchool;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getDepartureReason(): string
	{
		return $this->departureReason;
	}

	/**
	 * @param string $departureReason
	 */
	public function setDepartureReason(string $departureReason): Student
	{
		$this->departureReason = $departureReason;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getTransport(): string
	{
		return $this->transport;
	}

	/**
	 * @param string $transport
	 */
	public function setTransport(string $transport): Student
	{
		$this->transport = $transport;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getTransportNotes(): string
	{
		return $this->transportNotes;
	}

	/**
	 * @param string $transportNotes
	 */
	public function setTransportNotes(string $transportNotes): Student
	{
		$this->transportNotes = $transportNotes;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getDayType(): string
	{
		return $this->dayType;
	}

	/**
	 * @param string $dayType
	 *
	 * @return Student
	 */
	public function setDayType(string $dayType): Student
	{
		$this->dayType = $dayType;

		return $this;
	}

    /**
     * @return ArrayCollection
     */
    public function getRolls(): ArrayCollection
    {
        return $this->rolls;
    }

    /**
     * @param ArrayCollection $rolls
     * @return Student
     */
    public function setRolls(ArrayCollection $rolls): Student
    {
        $this->rolls = $rolls;
        return $this;
    }

    /**
     * @param RoleGroup $roleGroup
     * @param bool $addRollGroup
     * @return Student
     */
    public function addRoll(RoleGroup $roleGroup, $addRollGroup = true): Student
    {
        if ($addRollGroup)
            $roleGroup->addStudent($this, false);

        if (! $this->rolls->contains($roleGroup))
            $this->rolls->add($roleGroup);

        return $this;
    }

    /**
     * @param RoleGroup $roleGroup
     * @return Student
     */
    public function removeRoll(RoleGroup $roleGroup): Student
    {
        if ($this->rolls->contains($roleGroup))
            $this->rolls->removeElement($roleGroup);

        return $this;
    }
}
