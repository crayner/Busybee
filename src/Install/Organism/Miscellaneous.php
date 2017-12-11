<?php
namespace App\Install\Organism;


class Miscellaneous
{
	/**
	 * @var string
	 */
	private $secret;

	/**
	 * @var string
	 */
	private $sessionName = 'busybee_session';

	/**
	 * @var string
	 */
	private $sessionRememberMeName = 'busybee_session_remember';

	/**
	 * @var integer
	 */
	private $sessionMaxIdleTime = 900;

	/**
	 * @var integer
	 */
	private $signInCountMinimum = 3;

	/**
	 * @var string
	 */
	private $country;

	/**
	 * @var string
	 */
	private $timezone;

	/**
	 * @var string
	 */
	private $locale;

	/**
	 * @var integer
	 */
	private $passwordMinLength = 8;

	/**
	 * @var boolean
	 */
	private $passwordNumbers = true;

	/**
	 * @var boolean
	 */
	private $passwordMixedCase = true;

	/**
	 * @var boolean
	 */
	private $passwordSpecials = false;

	/**
	 * @var string
	 */
	private $hemisphere;

	/**
	 * @var string
	 */
	private $userEmail;

	/**
	 * @var string
	 */
	private $userName;

	/**
	 * @var string
	 */
	private $userPassword;

	/**
	 * @var boolean
	 */
	private $googleOAuth = false;

	/**
	 * @var string
	 */
	private $googleClientId;

	/**
	 * @var string
	 */
	private $googleClientSecret;

	/**
	 * Miscellaneous constructor.
	 */
	public function __construct()
	{
		$this->setSecret(null);
	}

	/**
	 * @return string
	 */
	public function getSecret(): string
	{
		return $this->secret;
	}

	/**
	 * @param string $secret
	 *
	 * @return Miscellaneous
	 */
	public function setSecret(string $secret = null): Miscellaneous
	{
		if (is_null($secret))
			$secret = uniqid('', true);

		$this->secret = $secret;

		return $this;
}

	/**
	 * @return string
	 */
	public function getSessionName(): string
	{
		return $this->sessionName;
	}

	/**
	 * @param string $sessionName
	 *
	 * @return Miscellaneous
	 */
	public function setSessionName(string $sessionName): Miscellaneous
	{
		$this->sessionName = $sessionName;

		return $this;
}

	/**
	 * @return int
	 */
	public function getSessionMaxIdleTime(): int
	{
		return $this->sessionMaxIdleTime;
	}

	/**
	 * @param int $sessionMaxIdleTime
	 *
	 * @return Miscellaneous
	 */
	public function setSessionMaxIdleTime(int $sessionMaxIdleTime): Miscellaneous
	{
		$this->sessionMaxIdleTime = $sessionMaxIdleTime;

		return $this;
}

	/**
	 * @return int
	 */
	public function getSignInCountMinimum(): int
	{
		return $this->signInCountMinimum;
	}

	/**
	 * @param int $signInCountMinimum
	 *
	 * @return Miscellaneous
	 */
	public function setSignInCountMinimum(int $signInCountMinimum): Miscellaneous
	{
		$this->signInCountMinimum = $signInCountMinimum;

		return $this;
}

	/**
	 * @return string
	 */
	public function getSessionRememberMeName(): string
	{
		return $this->sessionRememberMeName;
	}

	/**
	 * @param string $sessionRememberMeName
	 *
	 * @return Miscellaneous
	 */
	public function setSessionRememberMeName(string $sessionRememberMeName): Miscellaneous
	{
		$this->sessionRememberMeName = $sessionRememberMeName;

		return $this;
}

	/**
	 * @return string
	 */
	public function getCountry(): ?string
	{
		return $this->country;
	}

	/**
	 * @param string $country
	 *
	 * @return Miscellaneous
	 */
	public function setCountry(string $country = null): Miscellaneous
	{
		$this->country = $country;

		return $this;
}

	/**
	 * @return string
	 */
	public function getTimezone(): ?string
	{
		return $this->timezone;
	}

	/**
	 * @param string $timezone
	 *
	 * @return Miscellaneous
	 */
	public function setTimezone(string $timezone = null): Miscellaneous
	{
		$this->timezone = $timezone;

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
	 * @return Miscellaneous
	 */
	public function setPasswordMinLength(int $passwordMinLength): Miscellaneous
	{
		$this->passwordMinLength = $passwordMinLength;

		return $this;
}

	/**
	 * @return string
	 */
	public function getLocale(): ?string
	{
		return $this->locale;
	}

	/**
	 * @param string $locale
	 *
	 * @return Miscellaneous
	 */
	public function setLocale(string $locale = null): Miscellaneous
	{
		$this->locale = $locale;

		return $this;
}

	/**
	 * @return string
	 */
	public function getHemisphere(): ?string
	{
		return $this->hemisphere;
	}

	/**
	 * @param string $hemisphere
	 *
	 * @return Miscellaneous
	 */
	public function setHemisphere(string $hemisphere = null): Miscellaneous
	{
		$this->hemisphere = $hemisphere;

		return $this;
}

	/**
	 * @return bool
	 */
	public function isPasswordNumbers(): bool
	{
		return $this->passwordNumbers;
	}

	/**
	 * @param bool $passwordNumbers
	 *
	 * @return Miscellaneous
	 */
	public function setPasswordNumbers(bool $passwordNumbers): Miscellaneous
	{
		$this->passwordNumbers = $passwordNumbers;

		return $this;
}

	/**
	 * @return bool
	 */
	public function isPasswordMixedCase(): bool
	{
		return $this->passwordMixedCase;
	}

	/**
	 * @param bool $passwordMixedCase
	 *
	 * @return Miscellaneous
	 */
	public function setPasswordMixedCase(bool $passwordMixedCase): Miscellaneous
	{
		$this->passwordMixedCase = $passwordMixedCase;

		return $this;
}

	/**
	 * @return bool
	 */
	public function isPasswordSpecials(): bool
	{
		return $this->passwordSpecials;
	}

	/**
	 * @param bool $passwordSpecials
	 *
	 * @return Miscellaneous
	 */
	public function setPasswordSpecials(bool $passwordSpecials): Miscellaneous
	{
		$this->passwordSpecials = $passwordSpecials;

		return $this;
}

	/**
	 * @return string
	 */
	public function getUserEmail(): ?string
	{
		return $this->userEmail;
	}

	/**
	 * @param string $userEmail
	 *
	 * @return Miscellaneous
	 */
	public function setUserEmail(string $userEmail = null): Miscellaneous
	{
		$this->userEmail = $userEmail;

		return $this;
}

	/**
	 * @return string
	 */
	public function getUserName(): ?string
	{
		return $this->userName;
	}

	/**
	 * @param string $userName
	 *
	 * @return Miscellaneous
	 */
	public function setUserName(string $userName = null): Miscellaneous
	{
		$this->userName = $userName;

		return $this;
}

	/**
	 * @return string
	 */
	public function getUserPassword(): ?string
	{
		return $this->userPassword;
	}

	/**
	 * @param string $userPassword
	 *
	 * @return Miscellaneous
	 */
	public function setUserPassword(string $userPassword = null): Miscellaneous
	{
		$this->userPassword = $userPassword;

		return $this;
}

	/**
	 * @return bool
	 */
	public function isGoogleOAuth(): bool
	{
		return $this->googleOAuth;
	}

	/**
	 * @param bool $googleOAuth
	 *
	 * @return Miscellaneous
	 */
	public function setGoogleOAuth(bool $googleOAuth): Miscellaneous
	{
		$this->googleOAuth = $googleOAuth;

		return $this;
}

	/**
	 * @return string
	 */
	public function getGoogleClientId(): ?string
	{
		return $this->googleClientId;
	}

	/**
	 * @param string $googleClientId
	 *
	 * @return Miscellaneous
	 */
	public function setGoogleClientId(string $googleClientId = null): Miscellaneous
	{
		$this->googleClientId = $googleClientId;

		return $this;
}

	/**
	 * @return string
	 */
	public function getGoogleClientSecret(): ?string
	{
		return $this->googleClientSecret;
	}

	/**
	 * @param string $googleClientSecret
	 *
	 * @return Miscellaneous
	 */
	public function setGoogleClientSecret(string $googleClientSecret = null): Miscellaneous
	{
		$this->googleClientSecret = $googleClientSecret;

		return $this;
	}

	/**
	 * @param array $params
	 *
	 * @return array
	 */
	public function dumpMiscellaneousSettings(array $params)
	{
		foreach($params as $name=>$value)
		{
			$q = str_replace('_', ' ', $name);
			$q = explode(' ', $q);
			foreach($q as $e=>$w)
				$q[$e] = ucfirst($w);
			$q = implode('', $q);
			$get = 'get' . $q;
			$is = 'is' . $q;
			if (method_exists($this, $get))
				$params[$name] = $this->$get();
			elseif (method_exists($this, $is))
				$params[$name] = $this->$is();
		}

		if (! empty($this->getUserEmail()))
			$params['user_email'] = $this->getUserEmail();
		if (! empty($this->getUserName()))
			$params['user_name'] = $this->getUserName();
		if (! empty($this->getUserPassword()))
			$params['user_password'] = $this->getUserPassword();

		return $params;
	}
}