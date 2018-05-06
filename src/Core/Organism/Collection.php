<?php
namespace App\Core\Organism;

use Doctrine\Common\Collections\ArrayCollection;

class Collection
{
    /**
     * @var ArrayCollection
     */
    private $values;

    /**
     * @return ArrayCollection
     */
    public function getValues(): ArrayCollection
    {
        if (empty($this->values))
            $this->values = new ArrayCollection();
        return $this->values;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $values
     * @return Collection
     */
    public function setValues(?ArrayCollection $values): Collection
    {
        $this->values = $values;
        return $this;
    }


}