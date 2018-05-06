<?php
namespace App\School\Organism;

class Facility
{
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
     * @return Facility
     */
    public function setName(?string $name): Facility
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @var bool
     */
    private $teachingSpace;

    /**
     * @return bool
     */
    public function isTeachingSpace(): bool
    {
        return $this->teachingSpace ? true : false ;
    }

    /**
     * @param bool|null $teachingSpace
     * @return Facility
     */
    public function setTeachingSpace(?bool $teachingSpace): Facility
    {
        $this->teachingSpace = $teachingSpace ? true : false;
        return $this;
    }
}