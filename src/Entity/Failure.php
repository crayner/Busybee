<?php

namespace App\Entity;

/**
 * Failure
 */
class Failure
{
	/**
	 * @var integer
	 */
	private $id;

	/**
	 * @var string
	 */
	private $address;

	/**
	 * @var integer
	 */
	private $failures;

	/**
	 * @var \DateTime
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
	 * Construct
	 *
	 * @return    Failure
	 */
	public function __construct()
	{
		$this->failures     = 0;
		$this->address      = null;
		$this->createdOn    = null;
		$this->lastModified = null;

		return $this;
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
	 * Get address
	 *
	 * @return string
	 */
	public function getAddress()
	{
		return $this->address;
	}

	/**
	 * Set address
	 *
	 * @param string $address
	 *
	 * @return Failure
	 */
	public function setAddress($address)
	{
		$this->address = $address;

		return $this;
	}

	/**
	 * Get count
	 *
	 * @return \number
	 */
	public function getFailures()
	{
		return $this->failures;
	}

	/**
	 * Set failures
	 *
	 * @param \number $count
	 *
	 * @return Failure
	 */
	public function setFailures($failures)
	{
		$this->failures = intval($failures);

		return $this;
	}

	/**
	 * Inc count
	 *
	 * @return    Failure
	 */
	public function incFailures()
	{
		$this->failures++;

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
	 * @return Failure
	 */
	public function setLastModified(\DateTime $lastModified)
	{
		$this->lastModified = $lastModified;

		return $this;
	}

	/**
	 * @ORM\PrePersist
	 * @ORM\PreUpdate
	 */
	public function updateLastModified()
	{
		if (empty($this->getCreatedOn()))
			$this->setCreatedOn(new \DateTime('now'));
		$this->setLastModified(new \DateTime('now'));
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
	 * @return Failure
	 */
	public function setCreatedOn(\DateTime $createdOn)
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
	 * @return Failure
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
	 * @return Failure
	 */
	public function setModifiedBy(\Busybee\Core\SecurityBundle\Entity\User $modifiedBy = null)
	{
		$this->modifiedBy = $modifiedBy;

		return $this;
	}
}
