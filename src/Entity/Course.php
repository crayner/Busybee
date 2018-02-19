<?php
namespace App\Entity;
use App\School\Entity\CourseExtension;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;


/**
 * Course
 */
class Course extends CourseExtension
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
	private $code;

	/**
	 * @var string
	 */
	private $version;

	/**
	 * @var array
	 */
	private $targetYears;

    /**
     * @var null|boolean
     */
    private $map;

    /**
     * @var null|string
     */
    private $description;

    /**
     * @var null|Collection
     */
    private $calendars;

    /**
     * @var null|Department
     */
    private $department;

    /**
     * Course constructor.
     */
    public function __construct()
    {
        $this->calendars =  new ArrayCollection();
        $this->targetYears =  new ArrayCollection();
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
	 * Get code
	 *
	 * @return string
	 */
	public function getCode()
	{
		return strtoupper($this->code);
	}

	/**
	 * Set code
	 *
	 * @param string $code
	 *
	 * @return Course
	 */
	public function setCode($code)
	{
		$this->code = strtoupper($code);

		return $this;
	}

	/**
	 * Get version
	 *
	 * @return string
	 */
	public function getVersion()
	{
		return $this->version;
	}

	/**
	 * Set version
	 *
	 * @param string $version
	 *
	 * @return Course
	 */
	public function setVersion($version)
	{
		$this->version = $version;

		return $this;
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
	 * @return Course
	 */
	public function setName($name)
	{
		$this->name = $name;

		return $this;
	}

    /**
     * @return bool
     */
    public function getMap(): bool
    {
        return $this->map ? true : false ;
    }

    /**
     * @param bool|null $map
     * @return Course
     */
    public function setMap(?bool $map): Course
    {
        $this->map = $map ? true : false ;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param null|string $description
     * @return Course
     */
    public function setDescription(?string $description): Course
    {
        $this->description = $description ?: null ;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getCalendars(): ?Collection
    {
        if ($this->calendars instanceof PersistentCollection)
            $this->calendars->initialize();

        return $this->calendars;
    }

    /**
     * @param Collection $calendars
     * @return Course
     */
    public function setCalendars(?Collection $calendars): Course
    {
        $this->calendars = $calendars;

        return $this;
    }

    /**
     * @param Calendar|null $calendar
     * @return Course
     */
    public function addCalendar(?Calendar $calendar): Course
    {
        if (empty($calendar))
            return $this;

        $this->getCalendars();

        if (!$this->calendars->contains($calendar))
            $this->calendars->add($calendar);

        return $this;
    }

    /**
     * @param Calendar|null $calendar
     * @return Course
     */
    public function removeCalendar(?Calendar $calendar): Course
    {
        if (empty($calendar))
            return $this;

        $this->getCalendars();

        $this->calendars->removeElement($calendar);

        return $this;
    }

    /**
     * Get targetYears
     *
     * @return ArrayCollection
     */
    public function getTargetYears(): ArrayCollection
    {
        $this->initialiseTargetYears();

        return $this->targetYears;
    }

    /**
     * Set targetYears
     *
     * @param null|ArrayCollection $targetYear
     *
     * @return Course
     */
    public function setTargetYears(?ArrayCollection $targetYears)
    {
        if(empty($targetYears))
            $targetYears = $this->getTargetYears();

        $this->targetYears = $targetYears;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    private function initialiseTargetYears(): ArrayCollection
    {
        if (empty($this->targetYears))
            $this->targetYears = new ArrayCollection();

        if (is_array($this->targetYears))
            $this->targetYears = new ArrayCollection($this->targetYears);

        return $this->targetYears;
    }

    /**
     * @return Department|null
     */
    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    /**
     * @param Department|null $department
     * @return Course
     */
    public function setDepartment(?Department $department): Course
    {
        $this->department = $department;

        return $this;
    }
}
