<?php
namespace App\Entity;

use App\School\Entity\FaceToFaceExtension;

/**
 * Face to Face Class
 */
class FaceToFace extends FaceToFaceExtension
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

    public function getFullName()
    {
        $name = '';
        if ($this->getCourse() instanceof Course)
        {
            $name .= $this->getCourse()->getFullName(). ' ';
        }
        return $name . $this->getName();
    }
}
