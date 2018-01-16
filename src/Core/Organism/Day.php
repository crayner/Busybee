<?php
namespace App\Core\Organism;

use App\Core\Manager\SettingManager;

class Day
{
	/**
	 * @var \DateTime
	 */
	protected $date;

	/**
	 * @var array
	 */
	protected $parameters;

	/**
	 * @var SettingManager
	 */
	protected $settingManager;

	/**
	 * @var int
	 */
	private $firstDayofWeek;

	/**
	 * @var int
	 */
	private $lastDayofWeek;

	/**
	 * @var int|null
	 */
	private $weekNumber = null;

	/**
	 * @var bool
	 */
	private $termBreak = false;

	/**
	 * @var  bool
	 */
	private $closed;

	/**
	 * @var  bool
	 */
	private $special;

	/**
	 * @var null
	 */
	private $prompt;

	/**
	 * Day constructor.
	 *
	 * @param \DateTime               $date
	 * @param SettingManager          $sm
	 */
	public function __construct(\DateTime $date, SettingManager $settingManager, int $weeks)
	{
		$this->settingManager = $settingManager;
		$this->parameters     = [];
		$this->date           = $date;
		$this->day            = $date->format($this->settingManager->get('date.format.long'));
		$this->firstDayofWeek = $this->settingManager->get('firstDayofWeek', 'Monday') == 'Sunday' ? 7 : 1;
		$this->lastDayofWeek  = $this->settingManager->get('firstDayofWeek', 'Monday') == 'Sunday' ? 6 : 7;

		$this->setWeekNumber($weeks);
	}

	/**
	 * @param int $weekNumber
	 *
	 * @return Week
	 */
	public function setWeekNumber(int $weekNumber): Day
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

	public function getDate()
	{
		return $this->date;
	}

	public function getNumber()
	{
		return $this->date->format('j');
	}

	public function isFirstInWeek()
	{
		return $this->date->format('N') == $this->firstDayofWeek;
	}

	public function isLastInWeek()
	{
		return $this->date->format('N') == $this->lastDayofWeek;
	}

	public function isInWeek(Week $week)
	{
		return $this->date->format('W') == $week->getNumber();
	}

	public function isInMonth(Month $month)
	{
		return (($this->date->format('n') == $month->getNumber())
			&& ($this->date->format('Y') == $month->getYear()));
	}

	public function isInYear(Year $year)
	{
		return $this->date->format('Y') == $year;
	}

	public function setParameter($key, $value)
	{
		$this->parameters[$key] = $value;
	}

	public function getParameter($key)
	{
		return key_exists($key, $this->parameters) ? $this->parameters[$key] : null;
	}

	/**
	 * @return bool
	 */
	public function isSchoolDay(): bool
	{
		return $this->schoolDay ? true : false ;
	}

	/**
	 * @param bool $schoolDay
	 *
	 * @return Day
	 */
	public function setSchoolDay(bool $schoolDay): Day
	{
		$this->schoolDay = $schoolDay;

		return $this;
	}

	public function isTermBreak(): bool
	{
		return $this->termBreak ? true : false ;
	}

	public function setTermBreak(bool $termBreak): Day
	{
		$this->termBreak = (bool) $termBreak;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isClosed(): bool
	{
		return $this->closed ? true : false ;
	}

	/**
	 * @param $value
	 * @param $prompt
	 */
	public function setClosed(bool $value, string $prompt)
	{
		$this->closed = (bool) $value;
		$this->prompt = $prompt;
	}

	/**
	 * @return bool
	 */
	public function isSpecial(): bool
	{
		return $this->special ? true : false ;
	}

	/**
	 * @param $value
	 * @param $prompt
	 */
	public function setSpecial(bool $value, string $prompt)
	{
		$this->special = (bool) $value;
		$this->prompt  = $prompt;
	}

	/**
	 * @return null|string
	 */
	public function getPrompt(): ?string
	{
		return $this->prompt;
	}
}
