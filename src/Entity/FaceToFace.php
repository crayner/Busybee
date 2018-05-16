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
     * @var bool
     */
    private $useCourseName;

    /**
     * @return bool
     */
    public function isUseCourseName(): bool
    {
        return $this->useCourseName ? true : false ;
    }

    /**
     * @param bool $useCourseName
     * @return Activity
     */
    public function setUseCourseName(bool $useCourseName): Activity
    {
        $this->useCourseName = $useCourseName ? true : false ;
        return $this;
    }

    /**
     * canRemoveActivityFromCourse
     *
     * @return bool
     */
    public function canRemoveActivityFromCourse(): bool
    {
        if ($this->getStudents()->count() > 0)
            return false;
        return true;
    }
}
