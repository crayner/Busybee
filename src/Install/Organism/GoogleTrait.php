<?php
namespace App\Install\Organism;


trait GoogleTrait
{
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
     * @return $this
     */
    public function setGoogleOAuth(bool $googleOAuth)
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
     * @return $this
     */
    public function setGoogleClientId(?string $googleClientId)
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
     * @return $this
     */
    public function setGoogleClientSecret(?string $googleClientSecret)
    {
        $this->googleClientSecret = $googleClientSecret;

        return $this;
    }}