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

    /**
     * @var null|string
     */
    private $firstName;

    /**
     * @return null|string
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param null|string $firstName
     * @return User
     */
    public function setFirstName(?string $firstName): User
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @var null|string
     */
    private $surname;

    /**
     * @return null|string
     */
    public function getSurname(): ?string
    {
        return $this->surname;
    }

    /**
     * @param null|string $surname
     * @return User
     */
    public function setSurname(?string $surname): User
    {
        $this->surname = $surname;
        return $this;
    }

    /**
     * @var null|string
     */
    private $title;

    /**
     * @return null|string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param null|string $title
     * @return User
     */
    public function setTitle(?string $title): User
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @var null|string
     */
    private $orgName;

    /**
     * @return null|string
     */
    public function getOrgName(): ?string
    {
        return $this->orgName;
    }

    /**
     * @param null|string $orgName
     * @return User
     */
    public function setOrgName(?string $orgName): User
    {
        $this->orgName = $orgName;
        return $this;
    }

    /**
     * @var null|string
     */
    private $orgNameShort;

    /**
     * @return null|string
     */
    public function getOrgNameShort(): ?string
    {
        return $this->orgNameShort;
    }

    /**
     * @param null|string $orgNameShort
     * @return User
     */
    public function setOrgNameShort(?string $orgNameShort): User
    {
        $this->orgNameShort = $orgNameShort;
        return $this;
    }

    /**
     * @var null|string
     */
    private $country;

    /**
     * @return null|string
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * @param null|string $country
     * @return User
     */
    public function setCountry(?string $country): User
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @var null|string
     */
    private $currency;

    /**
     * @return null|string
     */
    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    /**
     * @param null|string $currency
     * @return User
     */
    public function setCurrency(?string $currency): User
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @var null|string
     */
    private $timezone;

    /**
     * @return null|string
     */
    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    /**
     * @param null|string $timezone
     * @return User
     */
    public function setTimezone(?string $timezone): User
    {
        $this->timezone = $timezone;
        return $this;
    }

    /**
     * @var boolean
     */
    private $googleOAuth;

    /**
     * @return bool
     */
    public function isGoogleOAuth(): bool
    {
        return $this->googleOAuth ? true : false;
    }

    /**
     * @param bool $googleOAuth
     * @return User
     */
    public function setGoogleOAuth(bool $googleOAuth): User
    {
        $this->googleOAuth = $googleOAuth ? true : false ;
        return $this;
    }

    /**
     * @var null|string
     */
    private $googleClientId;

    /**
     * @return null|string
     */
    public function getGoogleClientId(): ?string
    {
        return $this->googleClientId;
    }

    /**
     * @param null|string $googleClientId
     * @return User
     */
    public function setGoogleClientId(?string $googleClientId): User
    {
        $this->googleClientId = $googleClientId;
        return $this;
    }

    /**
     * @var null|string
     */
    private $googleClientSecret;

    /**
     * @return null|string
     */
    public function getGoogleClientSecret(): ?string
    {
        return $this->googleClientSecret;
    }

    /**
     * @param null|string $googleClientSecret
     * @return User
     */
    public function setGoogleClientSecret(?string $googleClientSecret): User
    {
        $this->googleClientSecret = $googleClientSecret;
        return $this;
    }
}