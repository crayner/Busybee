<?php
namespace App\Entity;

use App\Calendar\Entity\CalendarExtension;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;
use Hillrange\Form\Util\CollectionInterface;

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
	 * @var \Doctrine\Common\Collections\Collection
	 */
	private $specialDays;

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
	 * @param null|Term $term
	 *
	 * @return Calendar
	 */
	public function addTerm(?Term $term, $add = true): Calendar
	{
		if ($this->getTerms()->contains($term) || empty($term))
			return $this;

		if ($add)
		    $term->setCalendar($this, false);

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
	 * @return Collection
	 */
	public function getTerms(): Collection
	{
	    if (empty($this->terms))
	        $this->terms = new ArrayCollection();

	    if ($this->terms instanceof PersistentCollection && ! $this->terms->isInitialized())
	        $this->terms->initialize();

		return $this->terms;
	}

	/**
	 * Add specialDay
	 *
	 * @param SpecialDay|null $specialDay
	 *
	 * @return Calendar
	 */
	public function addSpecialDay(?SpecialDay $specialDay, $add = true): Calendar
	{
		if ($this->getSpecialDays()->contains($specialDay) || empty($specialDay))
		    return $this;

        if ($add)
            $specialDay->setCalendar($this, false);
        $this->specialDays->add($specialDay);

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
	 * @return null|Collection
	 */
	public function getSpecialDays(): Collection
	{
	    if (empty($this->specialDays))
	        $this->specialDays = new ArrayCollection();

		if ($this->specialDays instanceof PersistentCollection && ! $this->specialDays->isInitialized())
			$this->specialDays->initialize();

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
    public function getCalendarGrades(): Collection
    {
        if (empty($this->calendarGrades))
            $this->calendarGrades = new ArrayCollection();

        if ($this->calendarGrades instanceof PersistentCollection && ! $this->calendarGrades->isInitialized())
            $this->calendarGrades->initialize();

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

        if ($this->getCalendarGrades()->contains($calendarGrade))
            return $this;

        $this->calendarGrades->add($calendarGrade);

        return $this;
    }

    /**
     * @param CalendarGrade|null $calendarGrade
     * @param bool $remove
     * @return Calendar
     */
    public function removeCalendarGrade(?CalendarGrade $calendarGrade): Calendar
    {
        if (empty($calendarGrade))
            return $this;

        if ($this->getCalendarGrades()->contains($calendarGrade)) {
            $calendarGrade->setCalendar(null);
            $this->calendarGrades->removeElement($calendarGrade);
        }

        return $this;
    }
}