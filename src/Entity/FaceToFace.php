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
     * @var null|Course
     */
    private $course;

    /**
     * @return Course|null
     */
    public function getCourse(): ?Course
    {
        if ($this->course instanceof Course)
            $this->course->getId();
        return $this->course;
    }

    /**
     * @param Course|null $courses
     * @return FaceToFace
     */
    public function setCourse(?Course $course): FaceToFace
    {
        if (empty($course))
            $course = null;

        $this->course = $course;

        return $this;
    }

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
