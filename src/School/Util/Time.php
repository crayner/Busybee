<?php
namespace App\School\Util;


class Time
{
	/**
	 * @var \DateTime
	 */
	private $open;
	/**
	 * @var \DateTime
	 */
	private $begin;
	/**
	 * @var \DateTime
	 */
	private $finish;
	/**
	 * @var \DateTime
	 */
	private $close;

	/**
	 * @return \DateTime
	 */
	public function getOpen(): \DateTime
	{
		return $this->open;
	}

	/**
	 * @param \DateTime $open
	 *
	 * @return Time
	 */
	public function setOpen(\DateTime $open): Time
	{
		$this->open = $open;

		return $this;
	}

	/**
	 * @return \DateTime
	 */
	public function getBegin(): \DateTime
	{
		return $this->begin;
	}

	/**
	 * @param \DateTime $begin
	 *
	 * @return Time
	 */
	public function setBegin(\DateTime $begin): Time
	{
		$this->begin = $begin;

		return $this;
	}

	/**
	 * @return \DateTime
	 */
	public function getFinish(): \DateTime
	{
		return $this->finish;
	}

	/**
	 * @param \DateTime $finish
	 *
	 * @return Time
	 */
	public function setFinish(\DateTime $finish): Time
	{
		$this->finish = $finish;

		return $this;
	}

	/**
	 * @return \DateTime
	 */
	public function getClose(): \DateTime
	{
		return $this->close;
	}

	/**
	 * @param \DateTime $close
	 *
	 * @return Time
	 */
	public function setClose(\DateTime $close): Time
	{
		$this->close = $close;

		return $this;
	}
}