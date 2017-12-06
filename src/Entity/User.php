<?php

namespace App\Entity;

use Busybee\Core\SecurityBundle\Model\User as UserModel;
use Busybee\People\PersonBundle\Entity\Person;
use DateTime;

/**
 * User
 */
class User extends UserModel
{
	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $username;

	/**
	 * @var string
	 */
	protected $usernameCanonical;

	/**
	 * @var string
	 */
	protected $email;

	/**
	 * @var string
	 */
	protected $emailCanonical;

	/**
	 * @var boolean
	 */
	protected $enabled;

	/**
	 * @var string
	 */
	protected $locale;

	/**
	 * @var string
	 */
	protected $password;

	/**
	 * @var \DateTime
	 */
	protected $lastLogin;

	/**
	 * @var boolean
	 */
	protected $locked;

	/**
	 * @var boolean
	 */
	protected $expired;

	/**
	 * @var \DateTime
	 */
	protected $expiresAt;

	/**
	 * @var string
	 */
	protected $confirmationToken;

	/**
	 * @var \DateTime
	 */
	protected $passwordRequestedAt;

	/**
	 * @var boolean
	 */
	protected $credentialsExpired;

	/**
	 * @var \DateTime
	 */
	protected $credentialsExpireAt;

	/**
	 * @var \Doctrine\Common\Collections\Collection
	 */
	private $lastModified;

	/**
	 * @var \DateTime
	 */
	private $createdOn;

	/**
	 * @var \Busybee\Core\SecurityBundle\Entity\User
	 */
	private $createdBy;

	/**
	 * @var \Busybee\Core\SecurityBundle\Entity\User
	 */
	private $modifiedBy;

	/**
	 * @var array
	 */
	private $groups;

	/**
	 * @var array
	 */
	private $directroles;

	/**
	 * @var \Busybee\Core\CalendarBundle\Entity\Year
	 */
	private $year;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
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
	 * Get enabled
	 *
	 * @return boolean
	 */
	public function getEnabled()
	{
		return $this->enabled;
	}

	/**
	 * Set enabled
	 *
	 * @param boolean $enabled
	 *
	 * @return User
	 */
	public function setEnabled($enabled)
	{
		$this->enabled = $enabled;

		return $this;
	}

	/**
	 * Get locale
	 *
	 * @return string
	 */
	public function getLocale()
	{
		return $this->locale;
	}

	/**
	 * Set locale
	 *
	 * @param string $locale
	 *
	 * @return User
	 */
	public function setLocale($locale)
	{
		$this->locale = $locale;

		return $this;
	}

	/**
	 * Get password
	 *
	 * @return string
	 */
	public function getPassword()
	{
		return $this->password;
	}

	/**
	 * Set password
	 *
	 * @param string $password
	 *
	 * @return User
	 */
	public function setPassword($password)
	{
		$this->password = $password;

		return $this;
	}

	/**
	 * Get lastLogin
	 *
	 * @return \DateTime
	 */
	public function getLastLogin()
	{
		return $this->lastLogin;
	}

	/**
	 * Set lastLogin
	 *
	 * @param \DateTime $lastLogin
	 *
	 * @return User
	 */
	public function setLastLogin(DateTime $time = null)
	{
		$this->lastLogin = $time;

		return $this;
	}

	/**
	 * Get locked
	 *
	 * @return boolean
	 */
	public function getLocked()
	{
		return $this->locked;
	}

	/**
	 * Set locked
	 *
	 * @param boolean $locked
	 *
	 * @return User
	 */
	public function setLocked($locked)
	{
		$this->locked = $locked;

		return $this;
	}

	/**
	 * Get expired
	 *
	 * @return boolean
	 */
	public function getExpired()
	{
		return $this->expired;
	}

	/**
	 * Set expired
	 *
	 * @param boolean $expired
	 *
	 * @return User
	 */
	public function setExpired($expired)
	{
		$this->expired = $expired;

		return $this;
	}

	/**
	 * Get expiresAt
	 *
	 * @return \DateTime
	 */
	public function getExpiresAt()
	{
		return $this->expiresAt;
	}

	/**
	 * Set expiresAt
	 *
	 * @param \DateTime $expiresAt
	 *
	 * @return User
	 */
	public function setExpiresAt($expiresAt)
	{
		$this->expiresAt = $expiresAt;

		return $this;
	}

	/**
	 * Get confirmationToken
	 *
	 * @return string
	 */
	public function getConfirmationToken()
	{
		return $this->confirmationToken;
	}

	/**
	 * Set confirmationToken
	 *
	 * @param string $confirmationToken
	 *
	 * @return User
	 */
	public function setConfirmationToken($confirmationToken)
	{
		$this->confirmationToken = $confirmationToken;

		return $this;
	}

	/**
	 * Get passwordRequestedAt
	 *
	 * @return \DateTime
	 */
	public function getPasswordRequestedAt()
	{
		return $this->passwordRequestedAt;
	}

	/**
	 * Set passwordRequestedAt
	 *
	 * @param \DateTime $passwordRequestedAt
	 *
	 * @return User
	 */
	public function setPasswordRequestedAt(DateTime $passwordRequestedAt = null)
	{
		$this->passwordRequestedAt = $passwordRequestedAt;

		return $this;
	}

	/**
	 * Get credentialsExpired
	 *
	 * @return boolean
	 */
	public function getCredentialsExpired()
	{
		return $this->credentialsExpired;
	}

	/**
	 * Set credentialsExpired
	 *
	 * @param boolean $credentialsExpired
	 *
	 * @return User
	 */
	public function setCredentialsExpired($credentialsExpired)
	{
		$this->credentialsExpired = $credentialsExpired;

		return $this;
	}

	/**
	 * Get credentialsExpireAt
	 *
	 * @return \DateTime
	 */
	public function getCredentialsExpireAt()
	{
		return $this->credentialsExpireAt;
	}

	/**
	 * Set credentialsExpireAt
	 *
	 * @param \DateTime $credentialsExpireAt
	 *
	 * @return User
	 */
	public function setCredentialsExpireAt($credentialsExpireAt)
	{
		$this->credentialsExpireAt = $credentialsExpireAt;

		return $this;
	}

	/**
	 * check Username
	 *
	 * @return void
	 */
	public function checkUsername()
	{
		if (empty($this->getUsername()))
			$this->setUsername($this->getEmail());
		if (empty($this->getUsernameCanonical()))
			$this->setUsernameCanonical($this->getEmailCanonical());
	}

	/**
	 * Get username
	 *
	 * @return string
	 */
	public function getUsername()
	{
		return $this->username;
	}

	/**
	 * Set username
	 *
	 * @param string $username
	 *
	 * @return User
	 */
	public function setUsername($username)
	{
		$this->username = $username;

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
	 * @return User
	 */
	public function setEmail($email)
	{
		$this->email = $email;

		return $this;
	}

	/**
	 * Get usernameCanonical
	 *
	 * @return string
	 */
	public function getUsernameCanonical()
	{
		return $this->usernameCanonical;
	}

	/**
	 * Set usernameCanonical
	 *
	 * @param string $usernameCanonical
	 *
	 * @return User
	 */
	public function setUsernameCanonical($usernameCanonical)
	{
		$this->usernameCanonical = $usernameCanonical;

		return $this;
	}

	/**
	 * Get emailCanonical
	 *
	 * @return string
	 */
	public function getEmailCanonical()
	{
		return $this->emailCanonical;
	}

	/**
	 * Set emailCanonical
	 *
	 * @param string $emailCanonical
	 *
	 * @return User
	 */
	public function setEmailCanonical($emailCanonical)
	{
		$this->emailCanonical = $emailCanonical;

		return $this;
	}

	/**
	 * Get lastModified
	 *
	 * @return \DateTime
	 */
	public function getLastModified()
	{
		return $this->lastModified;
	}

	/**
	 * Set lastModified
	 *
	 * @param \DateTime $lastModified
	 *
	 * @return Setting
	 */
	public function setLastModified($lastModified)
	{
		$this->lastModified = $lastModified;

		return $this;
	}

	/**
	 * Get createdOn
	 *
	 * @return \DateTime
	 */
	public function getCreatedOn()
	{
		return $this->createdOn;
	}

	/**
	 * Set createdOn
	 *
	 * @param \DateTime $createdOn
	 *
	 * @return User
	 */
	public function setCreatedOn($createdOn)
	{
		$this->createdOn = $createdOn;

		return $this;
	}

	/**
	 * Get createdBy
	 *
	 * @return \Busybee\Core\SecurityBundle\Entity\User
	 */
	public function getCreatedBy()
	{
		return $this->createdBy;
	}

	/**
	 * Set createdBy
	 *
	 * @param \Busybee\Core\SecurityBundle\Entity\User $createdBy
	 *
	 * @return User
	 */
	public function setCreatedBy(\Busybee\Core\SecurityBundle\Entity\User $createdBy = null)
	{
		$this->createdBy = $createdBy;

		return $this;
	}

	/**
	 * Get modifiedBy
	 *
	 * @return \Busybee\Core\SecurityBundle\Entity\User
	 */
	public function getModifiedBy()
	{
		return $this->modifiedBy;
	}

	/**
	 * Set modifiedBy
	 *
	 * @param \Busybee\Core\SecurityBundle\Entity\User $modifiedBy
	 *
	 * @return User
	 */
	public function setModifiedBy(\Busybee\Core\SecurityBundle\Entity\User $modifiedBy = null)
	{
		$this->modifiedBy = $modifiedBy;

		return $this;
	}

	/**
	 * Get groups
	 *
	 * @return array
	 */
	public function getGroups()
	{
		if (empty($this->groups))
			$this->groups = [];

		return $this->groups;
	}

	/**
	 * Set groups
	 *
	 * @param array $groups
	 *
	 * @return User
	 */
	public function setGroups($groups)
	{
		$this->groups = $groups;

		return $this;
	}

	/**
	 * Get directroles
	 *
	 * @return array
	 */
	public function getDirectroles()
	{
		if (! is_array($this->directroles) && empty($this->directroles))
			$this->setDirectroles([]);

		return $this->directroles;
	}

	/**
	 * Set directroles
	 *
	 * @param array $directroles
	 *
	 * @return User
	 */
	public function setDirectroles($directroles)
	{
		$this->directroles = $directroles;

		return $this;
	}

    /**
     * Set year
     *
     * @param \Busybee\Core\CalendarBundle\Entity\Year $year
     *
     * @return User
     */
    public function setYear(\Busybee\Core\CalendarBundle\Entity\Year $year = null)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return \Busybee\Core\CalendarBundle\Entity\Year
     */
    public function getYear()
    {
        return $this->year;
    }
}
