<?php
namespace App\Entity;

use App\Calendar\Entity\TermExtension;

/**
 * Term
 */
class Term extends TermExtension
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
	private $nameShort;

	/**
	 * @var \DateTime
	 */
	private $firstDay;

	/**
	 * @var \DateTime
	 */
	private $lastDay;

	/**
	 * @var Calendar
	 */
	private $calendar;


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
	 * @return Term
	 */
	public function setName($name)
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * Get nameShort
	 *
	 * @return string
	 */
	public function getNameShort()
	{
		return $this->nameShort;
	}

	/**
	 * Set nameShort
	 *
	 * @param string $nameShort
	 *
	 * @return Term
	 */
	public function setNameShort($nameShort)
	{
		$this->nameShort = $nameShort;

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
	 * @return Term
	 */
	public function setFirstDay($firstDay)
	{
		$this->firstDay = $firstDay;

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
	 * @return Term
	 */
	public function setLastDay($lastDay)
	{
		$this->lastDay = $lastDay;

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
	 * @return Term
	 */
	public function setCalendar(?Calendar $calendar, $add = true)
	{
	    if (empty($calendar))
	        return $this;

	    if ($add)
	        $calendar->addTerm($this, false);

		$this->calendar = $calendar;

		return $this;
	}

    /**
     * @param int $id
     * @return Term
     */
    public function setId(int $id): Term
    {
        $this->id = $id;
        return $this;
    }
}
