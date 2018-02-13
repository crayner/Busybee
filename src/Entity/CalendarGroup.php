<?php
namespace App\Entity;

use App\Calendar\Entity\CalendarGroupExtension;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Hillrange\Security\Util\UserTrackInterface;
use Hillrange\Security\Util\UserTrackTrait;

/**
 * Calendar Group
 */
class CalendarGroup extends CalendarGroupExtension implements UserTrackInterface
{
	use UserTrackTrait;

	/**
	 * @var integer
	 */
	private $id;

	/**
	 * @var string
	 */
	private $nameShort;

	/**
	 * @var Calendar
	 */
	private $calendar;

	/**
	 * @var integer
	 */
	private $sequence;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var ArrayCollection
	 */
	private $rollGroups;

	/**
	 * @var Staff
	 */
	private $calendarTutor;

    /**
     * @var string
     */
    private $website;

    /**
     * @var collection
     */
    private $studentCalendars;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->rollGroups = new ArrayCollection();
		$this->studentCalendars = new ArrayCollection();
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
	 * Get nameShort
	 *
	 * @return string
	 */
	public function getNameShort(): ?string
	{
		return $this->nameShort;
	}

	/**
	 * Set nameShort
	 *
	 * @param string $nameShort
	 *
	 * @return CalendarGroup
	 */
	public function setNameShort($nameShort): CalendarGroup
	{
		$this->nameShort = $nameShort;

		return $this;
	}

	/**
	 * Get calendar
	 *
	 * @return Calendar
	 */
	public function getCalendar()
	{
		return $this->calendar;
	}

	/**
	 * Set calendar
	 *
	 * @param Calendar $calendar
	 *
	 * @return CalendarGroup
	 */
	public function setCalendar(Calendar $calendar = null)
	{
		$this->calendar = $calendar;

		return $this;
	}

	/**
	 * Get sequence
	 *
	 * @return integer
	 */
	public function getSequence()
	{
		return $this->sequence;
	}

	/**
	 * Set sequence
	 *
	 * @param integer $sequence
	 *
	 * @return CalendarGroup
	 */
	public function setSequence($sequence)
	{
		$this->sequence = $sequence;

		return $this;
	}

	/**
	 * Get name
	 *
	 * @return string
	 */
	public function getName()
	{
		if (empty($this->name))
			return $this->nameShort;

		return $this->name;
	}

	/**
	 * Set name
	 *
	 * @param string $name
	 *
	 * @return CalendarGroup
	 */
	public function setName($name)
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * Get rollGroups
	 *
	 * @return Collection
	 */
	public function getRollGroups()
	{
		return $this->rollGroups;
	}

	/**
	 * Set rollGroups
	 *
	 * @param ArrayCollection $rollGroups
	 *
	 * @return CalendarGroup
	 */
	public function setRollGroups(ArrayCollection $rollGroupsCalendarGroups): CalendarGroup
	{
		$this->rollGroups = $rollGroupsCalendarGroups;

		return $this;
	}

	/**
	 * Add Student
	 *
	 * @param RollGroup|null $rollGroups
	 *
	 * @return CalendarGroup
	 */
	public function addRollGroup(RollGroup $rollGroups = null, $add = true): CalendarGroup
	{
		if (!$rollGroups instanceof RollGroup)
			return $this;

		if ($add)
			$rollGroups->setCalendarGroup($this, false);

		if (is_null($this->rollGroups))
			$this->rollGroups = new ArrayCollection();

		if (!$this->rollGroups->contains($rollGroups))
			$this->rollGroups->add($rollGroups);

		return $this;
	}

	/**
	 * Remove Student
	 *
	 * @param RollGroup $rollGroups
	 *
	 * @return CalendarGroup
	 */
	public function removeRollGroup(RollGroup $rollGroups): CalendarGroup
	{
		$this->rollGroups->removeElement($rollGroups);

		return $this;
	}

	/**
	 * @return Staff|null
	 */
	public function getCalendarTutor(): ?Staff
	{
		return $this->calendarTutor;
	}

	/**
	 * @param Staff $CalendarTutor
	 *
	 * @return CalendarGroup
	 */
	public function setCalendarTutor(Staff $CalendarTutor = null): CalendarGroup
	{
		$this->calendarTutor = $CalendarTutor;

		return $this;
	}
	/**
	 * @return string|null
	 */
	public function getWebsite(): ?string
	{
		return $this->website;
	}

	/**
	 * @param string|null $website
	 *
	 * @return CalendarGroup
	 */
	public function setWebsite(string $website = null): CalendarGroup
	{
		$this->website = $website;

		return $this;
	}

    /**
     * @return Collection
     */
    public function getStudentCalendars(): Collection
    {
        return $this->studentCalendars;
    }

    /**
     * @param Collection $studentCalendars
     * @return CalendarGroup
     */
    public function setStudentCalendars(Collection $studentCalendars): CalendarGroup
    {
        $this->studentCalendars = $studentCalendars;

        return $this;
    }

    public function addStudentCalendar(StudentCalendar $studentCalendar): CalendarGroup
    {
        $studentCalendar->setCalendarGroup($this);

        if (! $this->studentCalendars->contains($studentCalendar))
            $this->studentCalendars->add($studentCalendar);

        return $this;
    }

    public function removeStudentCalendar(StudentCalendar $studentCalendar): CalendarGroup
    {
        if ($this->studentCalendars->contains($studentCalendar))
            $this->studentCalendars->removeElement($studentCalendar);

        return $this;
    }
}
