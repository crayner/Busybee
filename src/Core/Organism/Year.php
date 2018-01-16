<?php
namespace App\Core\Organism;

use App\Core\Manager\SettingManager;
use App\Entity\Calendar;
use Doctrine\Common\Collections\ArrayCollection;

class Year
{
	/**
	 * @var SettingManager
	 */
	private $settingManager;

	/**
	 * @var  ArrayCollection
	 */
	private $months;

	/**
	 * @var  ArrayCollection
	 */
	private $weeks;

	/**
	 * @var  ArrayCollection
	 */
	private $days;

	/**
	 * @var  array
	 */
	private $parameters;

	/**
	 * Year constructor.
	 *
	 * @param SettingManager $settingManager
	 */
	public function __construct(SettingManager $settingManager)
	{
		$this->settingManager = $settingManager;
	}

	/**
	 * Generate Year Calendar
	 *
	 * @param Calendar $calendar
	 *
	 * @return $this
	 */
	public function generate(Calendar $calendar)
	{
		$this->calendar       = $calendar;
		$this->months     = new ArrayCollection();
		$this->weeks      = new ArrayCollection();
		$this->days       = new ArrayCollection();
		$this->parameters = [];
		$oneDayInterval   = new \DateInterval('P1D');

		//Calculate first and last days of year
		$firstYearDate = \DateTime::createFromFormat('d.m.Y H:i:s', sprintf('01.%s 00:00:00', $this->calendar->getFirstDay()->format('m.Y')));
		$lastYearDate  = clone $firstYearDate;
		$lastYearDate->add(new \DateInterval('P1Y'));
		$lastYearDate->sub($oneDayInterval);

		//Calculate first and last days in calendar.
		//It's monday on the 1st week and sunday on the last week. or Sunday and Saturday
		$firstDate = clone $firstYearDate;
		$lastDate  = clone $lastYearDate;

		while ($firstDate->format('N') != $this->getFirstDayofWeek())
		{
			$firstDate->sub($oneDayInterval);
		}
		while ($lastDate->format('N') != $this->getLastDayofWeek())
		{
			$lastDate->add($oneDayInterval);
		}

		//Build calendar
		$dateIterator = clone $firstDate;
		$currentWeek  = null;
		$currentMonth = null;
		while ($dateIterator <= $lastDate)
		{
			$currentDate = clone $dateIterator;
			$day         = new Day($currentDate, $this->settingManager, count($this->weeks));
			$this->addDay($day);

			if (is_null($currentWeek))
				$currentWeek = new Week($this, $day, count($this->weeks) + 1);
			else
				$currentWeek->addDay($day);

			if ($currentDate >= $firstYearDate && $currentDate <= $lastYearDate)
			{
				if (is_null($currentMonth))
				{
					$currentMonth = new Month($this, $day);
				}
				elseif ($day->isInMonth($currentMonth))
				{
					$currentMonth->addDay($day);
					if ($currentDate == $lastYearDate)
					{
						$currentMonth->addWeek($currentWeek);
						$this->addWeek($currentWeek)
							->addMonth($currentMonth);
						if (count($currentWeek->getDays()) == 7) $currentWeek = null;
					}
				}
				elseif (!$day->isInMonth($currentMonth))
				{
					if (count($currentWeek->getDays()) > 1)
						$currentMonth->addWeek($currentWeek);
					$this->addMonth($currentMonth);
					$currentMonth = new Month($this, $day);
				}
				if ($currentWeek instanceof Week && count($currentWeek->getDays()) == 7)
				{
					$this->addWeek($currentWeek);
					$currentMonth->addWeek($currentWeek);
					$currentWeek = null;
				}
			}
			$dateIterator->add($oneDayInterval);
		}
		$this->initNames();

		return $this;
	}

	/**
	 * @return int
	 */
	public function getFirstDayofWeek(): int
	{
		return $this->settingManager->get('firstDayofWeek', 'Monday') == 'Sunday' ? 7 : 1;
	}

	/**
	 * @return int
	 */
	public function getLastDayofWeek(): int
	{
		return $this->settingManager->get('firstDayofWeek', 'Monday') == 'Sunday' ? 6 : 7;
	}

	/**
	 * @param Day $day
	 *
	 * @return $this
	 */
	public function addDay(Day $day): Year
	{
		if ($this->days->contains($day))
			return $this;

		$this->days->add($day);

		return $this;
	}

	/**
	 * @param Week $week
	 *
	 * @return $this
	 */
	public function addWeek(Week $week): Year
	{
		if ($this->weeks->contains($week))
			return $this;

		$this->weeks->add($week);

		return $this;
	}

	/**
	 * @param Month $month
	 *
	 * @return Year
	 */
	public function addMonth(Month $month): Year
	{
		if ($this->months->contains($month))
			return $this;

		$this->months->add($month);

		return $this;
	}

	/**
	 * Initiate Names
	 */
	private function initNames()
	{
		$w = strtotime('20180107');
		$this->monthFullNames  = [];
		$this->monthnameShorts = [];
		for($x=1; $x<=12; $x++)
		{
			$this->monthFullNames[] = strftime('%B', $w);
			$this->monthnameShorts[] = strftime('%b', $w);
			$w = $w + 2592000;
		}

		$w = strtotime('20180107');
		$this->weekFullNames   = [];
		$this->weeknameShorts  = [];
		for($x=1; $x<=7; $x++)
		{
			$this->weekFullNames[] = strftime('%A', $w);
			$this->weeknameShorts[] = strftime('%a', $w);
			$w = $w + 86400;
		}


		if ($this->settingManager->get('firstDayofWeek', 'Monday') === 'Monday')
		{
			$day = array_shift($this->weekFullNames);
			$this->weekFullNames[] = $day;
			$day = array_shift($this->weeknameShorts);
			$this->weeknameShorts[] = $day;
		}
	}

	/**
	 * @return SettingManager
	 */
	public function getSettingManager(): SettingManager
	{
		return $this->settingManager;
	}

	public function getMonths()
	{
		return $this->months;
	}

	public function getWeeks()
	{
		return $this->weeks;
	}

	public function getDays()
	{
		return $this->days;
	}

	public function getDay($param)
	{
		if (is_int($param))
		{
			return key_exists($param, $this->days) ? $this->days[$param] : null;
		}
		elseif (is_string($param))
		{
			foreach ($this->days as $day)
			{
				$date = $day->getDate()->format('d.m.Y');
				if ($date == $param)
					return $day;
			}

			return null;
		}
	}

	/**
	 * @return array
	 */
	public function getMonthFullNames(): array
	{
		return $this->monthFullNames;
	}

	/**
	 * @return array
	 */
	public function getWeeknameShorts(): array
	{
		return $this->weeknameShorts;
	}
}