<?php
namespace App\Calendar\Util;

class Week
{
	protected $year;
	protected $days;
	protected $weekNumber = null;
	protected $parameters;

	public function __construct(Year $year, Day $day, int $weekNumber)
	{
		$this->parameters = [];
		$this->year   = $year;
		$this->days       = [];
		$this->setWeekNumber($weekNumber)
			->addDay($day);
	}

	/**
	 * @param int $weekNumber
	 *
	 * @return Week
	 */
	public function setWeekNumber(int $weekNumber): Week
	{
		$this->weekNumber = $weekNumber;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getWeekNumber(): int
	{
		return $this->weekNumber;
	}

	public function addDay($day): Week
	{
		$day->setWeekNumber($this->getWeekNumber());
		$this->days[] = $day;

		return $this;
	}

	public function getDays()
	{
		return $this->days;
	}

	public function getYear()
	{
		$firstDay  = $this->days[count($this->days) - 1];
		$firstDate = $firstDay->getDate();

		return (int) $firstDate->format('Y');
	}

	public function isInMonth($month)
	{
		$months = $this->getMonths();
		foreach ($months as $monthIterator)
		{
			if ($monthIterator->getNumber() == $month->getNumber()
				&& $monthIterator->getYear() == $month->getYear())
				return true;
		}

		return false;
	}

	public function getMonths()
	{
		$_this  = $this;
		$months = array_filter($this->calendar->getMonths(), function ($month) use ($_this) {
			if (($_this->getFirstDate() < $month->getFirstDate()
					&& $_this->getLastDate() < $month->getFirstDate())
				|| ($_this->getFirstDate() > $month->getLastDate()
					&& $_this->getLastDate() > $month->getLastDate()))
			{
				return false;
			}
			else
			{
				return true;
			}
		});

		return $months;
	}

	public function getFirstDate()
	{
		return $this->getFirstDay()->getDate();
	}

	public function getFirstDay()
	{
		return $this->days[0];
	}

	public function getLastDate()
	{
		return $this->getLastDay()->getDate();
	}

	public function getLastDay()
	{
		return $this->days[count($this->days) - 1];
	}

	public function getFullName()
	{
		$fullNames = $this->calendar->getWeekFullNames();

		return $fullNames[$this->getNumber() - 1];
	}

	public function getNumber()
	{
		return (int) $this->weekNumber;
	}

	public function getnameShort()
	{
		$nameShorts = $this->calendar->getWeeknameShorts();

		return $nameShorts[$this->getNumber() - 1];
	}

	public function setParameter($key, $value)
	{
		$this->parameters[$key] = $value;
	}

	public function getParameter($key)
	{
		return key_exists($key, $this->parameters) ? $this->parameters[$key] : null;
	}
}
