<?php
namespace App\Entity;

use App\People\Entity\CareGiverExtension;

/**
 * CareGiver
 */
class CareGiver extends CareGiverExtension

{
	/**
	 * @var integer
	 */
	private $id;

	/**
	 * @var Person
	 */
	private $person;

	/**
	 * @var Family
	 */
	private $family;

	/**
	 * @var string
	 */
	private $comment;

	/**
	 * @var boolean
	 */
	private $phoneContact = true;

	/**
	 * @var boolean
	 */
	private $smsContact = true;

	/**
	 * @var boolean
	 */
	private $emailContact = true;

	/**
	 * @var boolean
	 */
	private $mailContact = false;

	/**
	 * @var integer
	 */
	private $contactPriority;

	/**
	 * @var string
	 */
	private $relationship = 'Unknown';
	/**
	 * @var boolean
	 */
	private $newsletter = true;

	/**
	 * @var boolean
	 */
	private $finance = true;

	/**
	 * @var boolean
	 */
	private $pickUpAllowed = true;

	/**
	 * @var boolean
	 */
	private $emergencyOnly = false;

	/**
	 * @var boolean
	 */
	private $reporting = true;

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
	 * Get person
	 *
	 * @return Person
	 */
	public function getPerson()
	{
		return $this->person;
	}

	/**
	 * Set person
	 *
	 * @param Person $person
	 *
	 * @return CareGiver
	 */
	public function setPerson(Person $person = null)
	{
		$this->person = $person;

		return $this;
	}

	/**
	 * Get family
	 *
	 * @return Family
	 */
	public function getFamily()
	{
		return $this->family;
	}

	/**
	 * Set family
	 *
	 * @param Family $family
	 *
	 * @return CareGiver
	 */
	public function setFamily(Family $family = null)
	{
		$this->family = $family;

		return $this;
	}

	/**
	 * Get comment
	 *
	 * @return string
	 */
	public function getComment()
	{
		return $this->comment;
	}

	/**
	 * Set comment
	 *
	 * @param string $comment
	 *
	 * @return CareGiver
	 */
	public function setComment($comment)
	{
		$this->comment = $comment;

		return $this;
	}

	/**
	 * Get phoneContact
	 *
	 * @return boolean
	 */
	public function getPhoneContact()
	{
		return $this->phoneContact;
	}

	/**
	 * Set phoneContact
	 *
	 * @param boolean $phoneContact
	 *
	 * @return CareGiver
	 */
	public function setPhoneContact($phoneContact)
	{
		$this->phoneContact = $phoneContact;

		return $this;
	}

	/**
	 * Get smsContact
	 *
	 * @return boolean
	 */
	public function getSmsContact()
	{
		return $this->smsContact;
	}

	/**
	 * Set smsContact
	 *
	 * @param boolean $smsContact
	 *
	 * @return CareGiver
	 */
	public function setSmsContact($smsContact)
	{
		$this->smsContact = $smsContact;

		return $this;
	}

	/**
	 * Get emailContact
	 *
	 * @return boolean
	 */
	public function getEmailContact()
	{
		return $this->emailContact;
	}

	/**
	 * Set emailContact
	 *
	 * @param boolean $emailContact
	 *
	 * @return CareGiver
	 */
	public function setEmailContact($emailContact)
	{
		$this->emailContact = $emailContact;

		return $this;
	}

	/**
	 * Get mailContact
	 *
	 * @return boolean
	 */
	public function getMailContact()
	{
		return $this->mailContact;
	}

	/**
	 * Set mailContact
	 *
	 * @param boolean $mailContact
	 *
	 * @return CareGiver
	 */
	public function setMailContact($mailContact)
	{
		$this->mailContact = $mailContact;

		return $this;
	}

	/**
	 * Get contactPriority
	 *
	 * @return integer
	 */
	public function getContactPriority()
	{
		return $this->contactPriority;
	}

	/**
	 * Set contactPriority
	 *
	 * @param integer $contactPriority
	 *
	 * @return CareGiver
	 */
	public function setContactPriority($contactPriority)
	{
		$this->contactPriority = $contactPriority;

		return $this;
	}

	/**
	 * Get relationship
	 *
	 * @return string
	 */
	public function getRelationship()
	{
		return $this->relationship;
	}

	/**
	 * Set relationship
	 *
	 * @param string $relationship
	 *
	 * @return CareGiver
	 */
	public function setRelationship($relationship)
	{
		$this->relationship = $relationship;

		return $this;
	}

	/**
	 * Get newsletter
	 *
	 * @return boolean
	 */
	public function getNewsletter()
	{
		return $this->newsletter;
	}

	/**
	 * Set newsletter
	 *
	 * @param boolean $newsletter
	 *
	 * @return CareGiver
	 */
	public function setNewsletter($newsletter)
	{
		$this->newsletter = $newsletter;

		return $this;
	}

	/**
	 * Get finance
	 *
	 * @return boolean
	 */
	public function getFinance()
	{
		return $this->finance;
	}

	/**
	 * Set finance
	 *
	 * @param boolean $finance
	 *
	 * @return CareGiver
	 */
	public function setFinance($finance)
	{
		$this->finance = $finance;

		return $this;
	}

	/**
	 * Get pickUpAllowed
	 *
	 * @return boolean
	 */
	public function getPickUpAllowed()
	{
		return $this->pickUpAllowed;
	}

	/**
	 * Set pickUpAllowed
	 *
	 * @param boolean $pickUpAllowed
	 *
	 * @return CareGiver
	 */
	public function setPickUpAllowed($pickUpAllowed)
	{
		$this->pickUpAllowed = $pickUpAllowed;

		return $this;
	}

	/**
	 * Get emergencyOnly
	 *
	 * @return boolean
	 */
	public function getEmergencyOnly()
	{
		return $this->emergencyOnly;
	}

	/**
	 * Set emergencyOnly
	 *
	 * @param boolean $emergencyOnly
	 *
	 * @return CareGiver
	 */
	public function setEmergencyOnly($emergencyOnly)
	{
		$this->emergencyOnly = $emergencyOnly;

		return $this;
	}

	/**
	 * Get reporting
	 *
	 * @return boolean
	 */
	public function getReporting()
	{
		return $this->reporting;
	}

	/**
	 * Set reporting
	 *
	 * @param boolean $reporting
	 *
	 * @return CareGiver
	 */
	public function setReporting($reporting)
	{
		$this->reporting = $reporting;

		return $this;
	}

    /**
     * @param int $id
     * @return CareGiver
     */
    public function setId(int $id): CareGiver
    {
        $this->id = $id;
        return $this;
    }
}
