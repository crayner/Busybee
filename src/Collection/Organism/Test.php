<?php
namespace App\Collection\Organism;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Test
{
    /**
     * @var null|Collection
     */
    private $values;

    /**
     * @return Collection|null
     */
    public function getValues(): ?Collection
    {
        if (empty($this->values))
            $this->values = new ArrayCollection();

        return $this->values;
    }

    /**
     * @param Collection|null $values
     * @return Test
     */
    public function setValues(?Collection $values): Test
    {
        $this->values = $values;
        return $this;
    }

    /**
     * @param Value $value
     * @return Test
     */
    public function addValue(Value $value): Test
    {
        if ($this->getValues()->contains($value))
            return $this;

        $this->values->add($value);

        return $this;
    }
}