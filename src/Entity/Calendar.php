<?php
namespace App\Entity;

use App\Calendar\Entity\CalendarExtension;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;

class Calendar extends CalendarExtension
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
	private $status;

	/**
	 * @var \DateTime
	 */
	private $firstDay;

	/**
	 * @var \DateTime
	 */
	private $lastDay;

	/**
	 * @var \Doctrine\Common\Collections\Collection
	 */
	private $terms;

	/**
	 * @var boolean
	 */
	private $termsSorted = false;

	/**
	 * @var \Doctrine\Common\Collections\Collection
	 */
	private $specialDays;

	/**
	 * @var boolean
	 */
	private $specialDaysSorted = false;

	/**
	 * @var string
	 */
	private $downloadCache;

	/**
	 * @var int|null
	 */
	private $importIdentifier;

    /**
     * @var null|Collection
     */
    private $calendarGrades;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->specialDays  = new ArrayCollection();
		$this->terms        = new ArrayCollection();
		$this->calendarGrades   = new ArrayCollection();
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
	 * Get status
	 *
	 * @return string
	 */
	public function getStatus()
	{
		return strtolower($this->status);
	}

	/**
	 * Set status
	 *
	 * @param $status
	 *
	 * @return $this
	 */
	public function setStatus($status): Calendar
	{
		$this->status = strtolower($status);

		return $this;
	}

	/**
	 * Get lastDay
	 *
	 * @return \DateTime
	 */
	public function getLastDay()
	{
		return $this->lastDay;
	}

	/**
	 * Set lastDay
	 *
	 * @param \DateTime $lastDay
	 *
	 * @return Calendar
	 */
	public function setLastDay($lastDay): Calendar
	{
		$this->lastDay = $lastDay;

		return $this;
	}

	/**
	 * Add term
	 *
	 * @param Term $term
	 *
	 * @return Calendar
	 */
	public function addTerm(Term $term): Calendar
	{
		if ($this->terms->contains($term))
			return $this;

		$term->setCalendar($this);

		$this->terms->add($term);

		return $this;
	}

	/**
	 * Remove term
	 *
	 * @param Term $term
	 */
	public function removeTerm(Term $term)
	{
		$this->terms->removeElement($term);
	}

	/**
	 * Get terms
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getTerms()
	{
		if (count($this->terms) == 0)
			$this->initialiseTerms();

		if (count($this->terms) == 0 || $this->termsSorted)
			return $this->terms;

		$iterator = $this->terms->getIterator();
		$iterator->uasort(function ($a, $b) {
			return ($a->getFirstDay() < $b->getFirstDay()) ? -1 : 1;
		});

		$this->terms = new ArrayCollection(iterator_to_array($iterator, false));

		$this->termsSorted = true;

		return $this->terms;
	}

	/**
	 * Initialise Terms
	 */
	public function initialiseTerms()
	{
		if ($this->terms instanceof PersistentCollection)
			$this->terms->initialize();
	}

	/**
	 * Add specialDay
	 *
	 * @param SpecialDay|null $specialDay
	 *
	 * @return Calendar
	 */
	public function addSpecialDay(SpecialDay $specialDay = null): Calendar
	{
		if (! $this->specialDays->contains($specialDay)) {
            $specialDay->setCalendar($this);
            $this->specialDays->add($specialDay);
        }

		return $this;
	}

	/**
	 * Remove specialDay
	 *
	 * @param SpecialDay $specialDay
	 */
	public function removeSpecialDay(SpecialDay $specialDay)
	{
		$this->specialDays->removeElement($specialDay);
	}

	/**
	 * Get specialDays
	 *
	 * @return null|\Doctrine\Common\Collections\Collection
	 */
	public function getSpecialDays($renew = false)
	{
		if ($this->specialDays instanceof PersistentCollection)
			$this->specialDays->initialize();

		if (count($this->specialDays) == 0)
			return null;

		if ($this->specialDaysSorted && !$renew)
			return $this->specialDays;

		$iterator = $this->specialDays->getIterator();
		$iterator->uasort(function ($a, $b) {
			return ($a->getDay() < $b->getDay()) ? -1 : 1;
		});
		$this->specialDays       = new ArrayCollection(iterator_to_array($iterator, false));
		$this->specialDaysSorted = true;

		return $this->specialDays;
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
	 * @return Calendar
	 */
	public function setName($name): Calendar
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * Get firstDay
	 *
	 * @return \DateTime
	 */
	public function getFirstDay()
	{
		return $this->firstDay;
	}

	/**
	 * Set firstDay
	 *
	 * @param \DateTime $firstDay
	 *
	 * @return Calendar
	 */
	public function setFirstDay($firstDay): Calendar
	{
		$this->firstDay = $firstDay;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getDownloadCache(): ?string
	{
		return $this->downloadCache;
	}

	/**
	 * @param string $downloadCache
	 *
	 * @return Calendar
	 */
	public function setDownloadCache(string $downloadCache = null): Calendar
	{

		$this->downloadCache = empty($downloadCache) ? null : $downloadCache;

		return $this;
	}

	/**
	 * @return int|null
	 */
	public function getImportIdentifier(): ?int
	{
		return $this->importIdentifier;
	}

	/**
	 * @param int|null $importIdentifier
	 *
	 * @return Calendar
	 */
	public function setImportIdentifier($importIdentifier): Calendar
	{
		$this->importIdentifier = $importIdentifier;

		return $this;
	}

    /**
     * @return Collection|null
     */
    public function getCalendarGrades(): ?Collection
    {
        return $this->calendarGrades;
    }

    /**
     * @param Collection|null $calendarGrades
     * @return Calendar
     */
    public function setCalendarGrades(?Collection $calendarGrades): Calendar
    {
        $this->calendarGrades = $calendarGrades;
        return $this;
    }


    /**
     * @param CalendarGrade|null $calendarGrade
     * @param bool $add
     * @return Calendar
     */
    public function addCalendarGrade(?CalendarGrade $calendarGrade, $add = true): Calendar
    {
        if (empty($calendarGrade))
            return $this;

        if ($add)
            $calendarGrade->setCalendar($this, false);

        if (!$this->calendarGrades->contains($calendarGrade))
            $this->calendarGrades->add($calendarGrade);

        return $this;
    }

    /**
     * @param CalendarGrade|null $calendarGrade
     * @param bool $remove
     * @return Calendar
     */
    public function removeCalendarGrade(?CalendarGrade $calendarGrade, $remove = true): Calendar
    {
        if (empty($calendarGrade))
            return $this;

        if ($this->calendarGrades->contains($calendarGrade)) {
            if ($remove)
                $calendarGrade->setCalendar(null);
            $this->calendarGrades->removeElement($calendarGrade);
        }

        return $this;
    }
}