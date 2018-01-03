<?php
namespace App\Entity;

use App\Core\EntityExtension\SpecialDayExtension;

/**
 * SpecialDay
 */
class SpecialDay extends SpecialDayExtension
{
	/**
	 * @var integer
	 */
	private $id;

	/**
	 * @var \DateTime
	 */
	private $day;

	/**
	 * @var string
	 */
	private $type;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $description;

	/**
	 * @var \DateTime
	 */
	private $open;

	/**
	 * @var \DateTime
	 */
	private $start;

	/**
	 * @var \DateTime
	 */
	private $finish;

	/**
	 * @var \DateTime
	 */
	private $close;

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
	 * Get day
	 *
	 * @return \DateTime
	 */
	public function getDay()
	{
		return $this->day;
	}

	/**
	 * Set day
	 *
	 * @param \DateTime $day
	 *
	 * @return SpecialDay
	 */
	public function setDay($day)
	{
		$this->day = $day;

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
	 * @return SpecialDay
	 */
	public function setType($type)
	{
		$this->type = $type;

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
	 * @return SpecialDay
	 */
	public function setName($name)
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * Get description
	 *
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * Set description
	 *
	 * @param string $description
	 *
	 * @return SpecialDay
	 */
	public function setDescription($description)
	{
		$this->description = $description;

		return $this;
	}

	/**
	 * Get calendar
	 *
	 * @return \Busybee\Core\CalendarBundle\Entity\Calendar
	 */
	public function getCalendar()
	{
		return $this->calendar;
	}

	/**
	 * Set calendar
	 *
	 * @param \Busybee\Core\CalendarBundle\Entity\Calendar $calendar
	 *
	 * @return SpecialDay
	 */
	public function setCalendar(\Busybee\Core\CalendarBundle\Entity\Calendar $calendar = null)
	{
		$this->calendar = $calendar;

		return $this;
	}

	/**
	 * Get open
	 *
	 * @return \DateTime
	 */
	public function getOpen()
	{
		return $this->open;
	}

	/**
	 * Set open
	 *
	 * @param \DateTime $open
	 *
	 * @return SpecialDay
	 */
	public function setOpen($open)
	{
		$this->open = $open;

		return $this;
	}

	/**
	 * Get start
	 *
	 * @return \DateTime
	 */
	public function getStart()
	{
		return $this->start;
	}

	/**
	 * Set start
	 *
	 * @param \DateTime $start
	 *
	 * @return SpecialDay
	 */
	public function setStart($start)
	{
		$this->start = $start;

		return $this;
	}

	/**
	 * Get finish
	 *
	 * @return \DateTime
	 */
	public function getFinish()
	{
		return $this->finish;
	}

	/**
	 * Set finish
	 *
	 * @param \DateTime $finish
	 *
	 * @return SpecialDay
	 */
	public function setFinish($finish)
	{
		$this->finish = $finish;

		return $this;
	}

	/**
	 * Get close
	 *
	 * @return \DateTime
	 */
	public function getClose()
	{
		return $this->close;
	}

	/**
	 * Set close
	 *
	 * @param \DateTime $close
	 *
	 * @return SpecialDay
	 */
	public function setClose($close)
	{
		$this->close = $close;

		return $this;
	}
}
