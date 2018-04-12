<?php
namespace App\Collection\Organism;


class Value
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Value
     */
    public function setId(int $id): Value
    {
        $this->id = $id;
        if (empty($this->getSequence()))
            $this->setSequence($id);

        return $this;
    }

    /**
     * @var string|null
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
     * @return Value
     */
    public function setName(?string $name): Value
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @var null|integer
     */
    private $sequence;

    /**
     * @return int|null
     */
    public function getSequence(): int
    {
        return $this->sequence ?: 0;
    }

    /**
     * @param int|null $sequence
     * @return Value
     */
    public function setSequence(?int $sequence): Value
    {
        $this->sequence = $sequence ?: 0;
        return $this;
    }

    public function __toString()
    {
        return $this->getName();
    }
}