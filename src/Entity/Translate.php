<?php
namespace App\Entity;

/**
 * Term
 */
class Translate
{
	/**
	 * @var integer
	 */
	private $id;

	/**
	 * Get id
	 *
	 * @return null|integer
	 */
	public function getId(): ?int
	{
		return $this->id;
	}

    /**
     * @param null|int $id
     * @return Translate
     */
    public function setId(?int $id): Translate
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @var null|string
     */
	private $source;

    /**
     * @return null|string
     */
    public function getSource(): ?string
    {
        return $this->source;
    }

    /**
     * @param null|string $source
     * @return Term
     */
    public function setSource(?string $source): Translate
    {
        $this->source = $source;
        return $this;
    }

    /**
     * @var null|string
     */
    private $locale;

    /**
     * @return null|string
     */
    public function getLocale(): ?string
    {
        return $this->locale;
    }

    /**
     * @param null|string $locale
     * @return Term
     */
    public function setLocale(?string $locale): Translate
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * @var null|string
     */
    private $value;

    /**
     * @return null|string
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @param null|string $value
     * @return Term
     */
    public function setValue(?string $value): Translate
    {
        $this->value = $value;
        return $this;
    }
}
