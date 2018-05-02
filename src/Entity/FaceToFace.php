<?php
namespace App\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;

/**
 * Face to Face Class
 */
class FaceToFace extends Activity
{
    /**
     * @var null|Scale
     */
    private $scale;

    /**
     * @return Scale|null
     */
    public function getScale(): ?Scale
    {
        return $this->scale;
    }

    /**
     * @param Scale|null $scale
     * @return FaceToFace
     */
    public function setScale(?Scale $scale): FaceToFace
    {
        if (empty($scale))
            $scale = null;

        $this->scale = $scale;

        return $this;
    }

    /**
     * @var null|Collection
     */
    private $periods;

    /**
     * @return Collection|null
     */
    public function getPeriods(): ?Collection
    {
        if (empty($this->periods))
            $this->periods = new ArrayCollection();

        if ($this->periods instanceof PersistentCollection && ! $this->periods->isInitialized())
            $this->periods->initialize();

        return $this->periods;
    }

    /**
     * @param Collection|null $periods
     * @return FaceToFace
     */
    public function setPeriods(?Collection $periods): FaceToFace
    {
        $this->periods = $periods;
        return $this;
    }

    /**
     * @param TimetablePeriodActivity|null $period
     * @param bool $add
     * @return FaceToFace
     */
    public function addPeriod(?TimetablePeriodActivity $period, $add = true): FaceToFace
    {
        if ($period instanceof TimetablePeriodActivity || $this->getPeriods()->contains($period))
            return $this;

        if ($add)
            $period->setActivity($this, false);

        $this->periods->add($period);

        return $this;
    }

    /**
     * @param TimetablePeriodActivity|null $period
     * @return FaceToFace
     */
    public function removePeriod(?TimetablePeriodActivity $period): FaceToFace
    {
        $this->getPeriods()->removeElement($period);

        return $this;
    }
}
