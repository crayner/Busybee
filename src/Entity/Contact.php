<?php
namespace App\Entity;

use App\People\Entity\ContactExtension;

/**
 * Staff
 */
class Contact extends ContactExtension
{
	/**
	 * @var string
	 */
	private $jobTitle;

	/**
	 * @var string
	 */
	private $profession;

	/**
	 * @var string
	 */
	private $employer;

	/**
	 * Get jobTitle
	 *
	 * @return string
	 */
	public function getJobTitle()
	{
		if (empty($this->jobTitle))
			$this->setJobTitle('Not Specified');

		return $this->jobTitle;
	}

	/**
	 * Set jobTitle
	 *
	 * @param string $jobTitle
	 *
	 * @return Staff
	 */
	public function setJobTitle($jobTitle): Contact
	{
		$this->jobTitle = $jobTitle;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getProfession(): string
	{
		return $this->profession;
	}

	/**
	 * @param string $profession
	 */
	public function setProfession(string $profession): Contact
	{
		$this->profession = $profession;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getEmployer(): string
	{
		return $this->employer;
	}

	/**
	 * @param string $employer
	 */
	public function setEmployer(string $employer): Contact
	{
		$this->employer = $employer;

		return $this;
	}
}
