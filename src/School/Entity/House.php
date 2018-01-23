<?php

namespace App\School\Entity;

class House
{
	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $shortName;

	/**
	 * @var string
	 */
	private $logo;

	/**
	 * @var int
	 */
	private $status = -1;

	/**
	 * @return string|null
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 *
	 * @return House
	 */
	public function setName(string $name): House
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getShortName()
	{
		return $this->shortName;
	}

	/**
	 * @param string $shortName
	 *
	 * @return House
	 */
	public function setShortName(string $shortName): House
	{
		$this->shortName = $shortName;

		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getLogo()
	{
		return $this->logo;
	}

	/**
	 * @param string $logo
	 * @param bool   $ignore
	 *
	 * @return House
	 */
	public function setLogo(string $logo = null, $ignore = true): House
	{
		if (empty($logo) && $ignore)
			return $this;

		$this->logo = $logo;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getStatus(): int
	{
		return $this->status;
	}

	/**
	 * @param int $status
	 *
	 * @return House
	 */
	public function setStatus(int $status): House
	{
		$this->status = $status;

		return $this;
	}
}