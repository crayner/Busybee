<?php
namespace App\Entity;


class Scale
{
    /**
     * @var null|string
     */
    private $name;

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param null|string $name
     * @return Scale
     */
    public function setName(?string $name): Scale
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @var null|string
     */
    private $nameShort;

    /**
     * @return null|string
     */
    public function getNameShort(): ?string
    {
        return $this->nameShort;
    }

    /**
     * @param null|string $nameShort
     * @return Scale
     */
    public function setNameShort(?string $nameShort): Scale
    {
        $this->nameShort = $nameShort;
        return $this;
    }

    /**
     * @var null|string
     */
    private $usage;

    /**
     * @return null|string
     */
    public function getUsage(): ?string
    {
        return $this->usage;
    }

    /**
     * @param null|string $usage
     * @return Scale
     */
    public function setUsage(?string $usage): Scale
    {
        $this->usage = $usage;
        return $this;
    }

    /**
     * @var null|string
     */
    private $lowestAcceptable;

    /**
     * @return null|string
     */
    public function getLowestAcceptable(): ?string
    {
        return $this->lowestAcceptable;
    }

    /**
     * @param null|string $lowestAcceptable
     * @return Scale
     */
    public function setLowestAcceptable(?string $lowestAcceptable): Scale
    {
        $this->lowestAcceptable = $lowestAcceptable;
        return $this;
    }

    /**
     * @var bool
     */
    private $active;

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        $this->setActive($this->active);

        return $this->active;
    }

    /**
     * @param bool $active
     * @return Scale
     */
    public function setActive(bool $active): Scale
    {
        $this->active = $active ? true : false ;

        return $this;
    }

    /**
     * @var bool
     */
    private $numeric;

    /**
     * @return bool
     */
    public function isNumeric(): bool
    {
        $this->setNumeric($this->numeric);

        return $this->numeric;
    }

    /**
     * @param bool $numeric
     * @return Scale
     */
    public function setNumeric(bool $numeric): Scale
    {
        $this->numeric = $numeric ? true : false ;

        return $this;
    }

    /**
     * @var null|int
     */
    private $id;

    /**
     * @return null|int
     */
    public function getId(): ?int
    {
        return $this->id;
    }
}