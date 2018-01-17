<?php
namespace App\School\Util;


class Day
{
	/**
	 * @var bool
	 */
	private $sun = false;
	/**
	 * @var bool
	 */
	private $mon = false;
	/**
	 * @var bool
	 */
	private $tue = false;
	/**
	 * @var bool
	 */
	private $wed = false;
	/**
	 * @var bool
	 */
	private $thu = false;
	/**
	 * @var bool
	 */
	private $fri = false;
	/**
	 * @var bool
	 */
	private $sat = false;

	/**
	 * @return bool
	 */
	public function isSun(): bool
	{
		return $this->sun;
	}

	/**
	 * @param bool $sun
	 *
	 * @return Day
	 */
	public function setSun(bool $sun = null): Day
	{
		$this->sun = $sun ? true : false ;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isMon(): bool
	{
		return $this->mon;
	}

	/**
	 * @param bool $mon
	 *
	 * @return Day
	 */
	public function setMon(bool $mon = null): Day
	{
		$this->mon = $mon ? true : false ;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isTue(): bool
	{
		return $this->tue;
	}

	/**
	 * @param bool $tue
	 *
	 * @return Day
	 */
	public function setTue(bool $tue = null): Day
	{
		$this->tue = $tue ? true : false ;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isWed(): bool
	{
		return $this->wed;
	}

	/**
	 * @param bool $wed
	 *
	 * @return Day
	 */
	public function setWed(bool $wed = null): Day
	{
		$this->wed = $wed ? true : false ;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isThu(): bool
	{
		return $this->thu;
	}

	/**
	 * @param bool $thu
	 *
	 * @return Day
	 */
	public function setThu(bool $thu = null): Day
	{
		$this->thu = $thu ? true : false ;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isFri(): bool
	{
		return $this->fri;
	}

	/**
	 * @param bool $fri
	 *
	 * @return Day
	 */
	public function setFri(bool $fri = null): Day
	{
		$this->fri = $fri ? true : false ;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isSat(): bool
	{
		return $this->sat;
	}

	/**
	 * @param bool $sat
	 *
	 * @return Day
	 */
	public function setSat(bool $sat = null): Day
	{
		$this->sat = $sat ? true : false ;

		return $this;
	}
}