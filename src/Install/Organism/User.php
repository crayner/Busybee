<?php
namespace App\Install\Organism;

class User
{
	/**
	 * @var string
	 */
	private $_email;

	/**
	 * @var string
	 */
	private $_username;

	/**
	 * @var string
	 */
	private $_password;

	/**
	 * @var bool
	 */
	private $passwordNumbers;

	/**
	 * @var bool
	 */
	private $passwordMixedCase;

	/**
	 * @var bool
	 */
	private $passwordSpecials;

	/**
	 * @var integer
	 */
	private $passwordMinLength;

	/**
	 * @return string
	 */
	public function getEmail(): ?string
	{
		return $this->_email;
	}

	/**
	 * @param string $_email
	 *
	 * @return Miscellaneous
	 */
	public function setEmail(string $_email = null): User
	{
		$this->_email = $_email;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getUsername(): ?string
	{
		return $this->_username;
	}

	/**
	 * @param string $_username
	 *
	 * @return Miscellaneous
	 */
	public function setUsername(string $_username = null): User
	{
		$this->_username = $_username;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getPassword(): ?string
	{
		return $this->_password;
	}

	/**
	 * @param string $_password
	 *
	 * @return Miscellaneous
	 */
	public function setPassword(string $_password = null): User
	{
		$this->_password = $_password;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isPasswordNumbers(): bool
	{
		return $this->passwordNumbers ? true : false ;
	}

	/**
	 * @param bool $passwordNumbers
	 *
	 * @return User
	 */
	public function setPasswordNumbers(bool $passwordNumbers): User
	{
		$this->passwordNumbers = $passwordNumbers;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isPasswordMixedCase(): bool
	{
		return $this->passwordMixedCase ? true : false ;
	}

	/**
	 * @param bool $passwordMixedCase
	 *
	 * @return User
	 */
	public function setPasswordMixedCase(bool $passwordMixedCase): User
	{
		$this->passwordMixedCase = $passwordMixedCase;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isPasswordSpecials(): bool
	{
		return $this->passwordSpecials ? true : false ;
	}

	/**
	 * @param bool $passwordSpecials
	 *
	 * @return User
	 */
	public function setPasswordSpecials(bool $passwordSpecials): User
	{
		$this->passwordSpecials = $passwordSpecials;

		return $this;
}

	/**
	 * @return int
	 */
	public function getPasswordMinLength(): int
	{
		return $this->passwordMinLength;
	}

	/**
	 * @param int $passwordMinLength
	 *
	 * @return User
	 */
	public function setPasswordMinLength(int $passwordMinLength): User
	{
		$this->passwordMinLength = $passwordMinLength;

		return $this;
}
}