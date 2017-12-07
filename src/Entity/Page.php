<?php
namespace App\Entity;

use App\Core\EntityExtension\PageModel;


/**
 * Page
 */
class Page extends PageModel
{
	/**
	 * @var integer
	 */
	private $id;

	/**
	 * @var string
	 */
	private $route;

	/**
	 * @var \DateTime
	 */
	private $lastModified;

	/**
	 * @var \DateTime
	 */
	private $createdOn;

	/**
	 * @var \App\Entity\User
	 */
	private $createdBy;

	/**
	 * @var \App\Entity\User
	 */
	private $modifiedBy;

	/**
	 * @var array
	 */
	private $roles;

	/**
	 * @var string
	 */
	private $path;

	/**
	 * @var \DateTime
	 */
	private $cacheTime;

	/**
	 * Page constructor.
	 */
	public function __construct()
	{
		$this->setCacheTime();
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
	 * Get route
	 *
	 * @return string
	 */
	public function getRoute()
	{
		return $this->route;
	}

	/**
	 * Set route
	 *
	 * @param string $route
	 *
	 * @return Page
	 */
	public function setRoute($route)
	{
		$this->route = $route;

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
	 * @return Page
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
	 * @return Page
	 */
	public function setCreatedOn($createdOn)
	{
		$this->createdOn = $createdOn;

		return $this;
	}

	/**
	 * Get createdBy
	 *
	 * @return \App\Entity\User
	 */
	public function getCreatedBy()
	{
		return $this->createdBy;
	}

	/**
	 * Set createdBy
	 *
	 * @param \App\Entity\User $createdBy
	 *
	 * @return Page
	 */
	public function setCreatedBy(\App\Entity\User $createdBy = null)
	{
		$this->createdBy = $createdBy;

		return $this;
	}

	/**
	 * Get modifiedBy
	 *
	 * @return \App\Entity\User
	 */
	public function getModifiedBy()
	{
		return $this->modifiedBy;
	}

	/**
	 * Set modifiedBy
	 *
	 * @param \App\Entity\User $modifiedBy
	 *
	 * @return Page
	 */
	public function setModifiedBy(\App\Entity\User $modifiedBy = null)
	{
		$this->modifiedBy = $modifiedBy;

		return $this;
	}

	/**
	 * Get roles
	 *
	 * @return array
	 */
	public function getRoles()
	{
		if (empty($this->roles))
			$this->roles = [];

		return $this->roles;
	}

	/**
	 * Set roles
	 *
	 * @param array $roles
	 *
	 * @return Page
	 */
	public function setRoles($roles)
	{
		foreach ($roles as $q => $w)
			if (is_null($w))
				unset($roles[$q]);

		$this->roles = empty($roles) ? [] : $roles;

		return $this;
	}

	/**
	 * Get path
	 *
	 * @return string
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * Set path
	 *
	 * @param string $path
	 *
	 * @return Page
	 */
	public function setPath($path)
	{
		$this->path = $path;

		return $this;
	}

	/**
	 * Get CacheTime
	 *
	 * @return \DateTime
	 */
	public function getCacheTime(): \DateTime
	{
		if (empty($this->cacheTime))
			$this->cacheTime = new \DateTime('-16 Minutes');

		return $this->cacheTime;
	}

	/**
	 * Set CacheTime
	 *
	 * @param \DateTime|null $cacheTime
	 *
	 * @return Page
	 */
	public function setCacheTime(\DateTime $cacheTime = null): Page
	{
		if (empty($cacheTime))
			$cacheTime = new \DateTime('Now');
		$this->cacheTime = $cacheTime;

		return $this;
	}
}
