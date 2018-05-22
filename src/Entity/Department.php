<?php
namespace App\Entity;

use App\Core\Exception\Exception;
use App\School\Entity\DepartmentExtension;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;

/**
 * Department
 */
class Department extends DepartmentExtension
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
	 * @var string
	 */
	private $type;

	/**
	 * @var string
	 */
	private $code;

	/**
	 * @var \Doctrine\Common\Collections\Collection
	 */
	private $members;

	/**
	 * @var \Doctrine\Common\Collections\Collection
	 */
	private $courses;

	/**
	 * @var string
	 */
	private $importIdentifier;

	/**
	 * @var string
	 */
	private $blurb;

	/**
	 * @var string
	 */
	private $logo;

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
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Set name
	 *
	 * @param string $name
	 *
	 * @return Department
	 */
	public function setName($name)
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * Get type
	 *
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * Set type
	 *
	 * @param string $type
	 *
	 * @return Department
	 */
	public function setType($type)
	{
		$this->type = $type;

		return $this;
	}

	/**
	 * Get code
	 *
	 * @return string
	 */
	public function getCode()
	{
		return $this->code;
	}

	/**
	 * Set code
	 *
	 * @param string $code
	 *
	 * @return Department
	 */
	public function setCode($code)
	{
		$this->code = $code;

		return $this;
	}

    /**
     * Add member
     *
     * @param DepartmentMember $member
     * @param bool $add
     * @return Department
     * @throws Exception
     */
	public function addMember(DepartmentMember $member, $add = true): Department
	{
	    if (empty($member) || $this->getMembers()->contains($member))
            return $this;

	    if ($add)
		    $member->setDepartment($this);

		$this->members->add($member);

		return $this;
	}

	/**
	 * Remove member
	 *
	 * @param DepartmentMember $member
	 */
	public function removeMember(DepartmentMember $member): Department
	{
		$this->members->removeElement($member);

		return $this;
	}

	/**
	 * Get member
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getMembers($sort = true)
	{
	    if (empty($this->members))
            $this->members = new ArrayCollection();

		if ($this->members instanceof PersistentCollection)
		    $this->memberInitialise($this->members);

	    if ($sort)
			return $this->sortMembers();

		return $this->members;
	}

	/**
	 * Set member
	 *
	 * @return Department
	 */
	public function setMembers(ArrayCollection $members): Department
	{
		$this->members = $members;

		return $this;
	}

	/**
	 * Add course
	 *
	 * @param Course $course
	 *
	 * @return Department
	 */
	public function addCourse(Course $course, $add = true)
	{
	    if (empty($course) || $this->getCourses()->contains($course))
	        return $this ;

	    if ($add)
	        $course->setDepartment($this, false);

		if ($this->courses->contains($course))
			return $this;

		$this->courses->add($course);

		return $this;
	}

	/**
	 * Remove course
	 *
	 * @param Course $course
	 */
	public function removeCourse(Course $course): Department
	{
	    if (empty($course))
	        return $this;

	    $this->getCourses()->removeElement($course);

	    $course->setDepartment(null, false);

		return $this;
	}

	/**
	 * Get courses
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getCourses($sorted = true)
	{
	    if (empty($this->courses))
	        $this->courses = new ArrayCollection();

	    if ($this->courses instanceof PersistentCollection)
            $this->courses->initialize();

		if ($sorted)
			return $this->sortCourses();

		return $this->courses;
	}

	/**
	 * Set courses
	 *
	 * @return Department
	 */
	public function setCourses(ArrayCollection $courses)
	{
		$this->courses = $courses;

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
	 * @return Department
	 */
	public function setImportIdentifier(string $importIdentifier): Department
	{
		$this->importIdentifier = $importIdentifier;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getBlurb(): ?string
	{
		return $this->blurb;
	}

	/**
	 * @param string $blurb
	 *
	 * @return Department
	 */
	public function setBlurb(string $blurb = null): Department
	{
		$this->blurb = $blurb;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getLogo(): ?string
	{
		return $this->logo;
	}

	/**
	 * @param string $logo
	 *
	 * @return Department
	 */
	public function setLogo(string $logo = null): Department
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * @param int $id
     * @return Department
     */
    public function setId(int $id): Department
    {
        $this->id = $id;
        return $this;
    }
}
