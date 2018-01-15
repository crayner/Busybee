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
	public function setDay($day): SpecialDay
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
	public function setType($type): SpecialDay
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
	public function setName($name): SpecialDay
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
	public function setDescription($description): SpecialDay
	{
		$this->description = $description;

		return $this;
	}

	/**
	 * Get calendar
	 *
	 * @return Calendar
	 */
	public function getCalendar(): ?Calendar
	{
		return $this->calendar;
	}

	/**
	 * Set calendar
	 *
	 * @param Calendar $calendar
	 *
	 * @return SpecialDay
	 */
	public function setCalendar(Calendar $calendar = null): SpecialDay
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
	public function setOpen($open): SpecialDay
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
	public function setStart($start): SpecialDay
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
	public function setFinish($finish): SpecialDay
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
	public function setClose($close): SpecialDay
	{
		$this->close = $close;

		return $this;
	}
}
