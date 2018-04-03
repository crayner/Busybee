<?php
namespace App\Timetable\Extension;

use App\Entity\Space;
use App\Entity\Staff;
use Hillrange\Security\Util\UserTrackInterface;
use Hillrange\Security\Util\UserTrackTrait;

abstract class TimetablePeriodActivityExtension implements UserTrackInterface
{
    use UserTrackTrait;

    /**
     * @var bool
     */
    private $local;

    /**
     * @var Space
     */
    private $inheritedSpace;

    /**
     * @var Staff
     */
    private $inheritedTutor1;

    /**
     * @var Staff
     */
    private $inheritedTutor2;

    /**
     * @var Staff
     */
    private $inheritedTutor3;

    /**
     * TimetablePeriodActivityExtension constructor.
     */
    public function __construct()
    {
        $this->setLocal(false);
    }

    /**
     * Get Local Tutor1
     *
     * @return Staff|null
     */
    public function getLocalTutor1()
    {
        $this->local = false;

        $this->inheritedTutor1 = $this->getTutor1();
        $tutor = $this->inheritedTutor1;

        $this->setTutor1(null);

        if ($this->inheritedTutor1 instanceof Staff && $this->getTutor1() instanceof Staff && $this->inheritedTutor1->getId() === $this->getTutor1()->getId()) {
            $tutor = null;
        }

        $this->setTutor1($tutor);

        $this->local = true;
        return $tutor;
    }

    /**
     * Get Local Tutor2
     *
     * @return Staff|null
     */
    public function getLocalTutor2()
    {
        $this->local = false;

        $this->inheritedTutor2 = $this->getTutor2();
        $tutor = $this->inheritedTutor2;

        $this->setTutor2(null);

        if ($this->inheritedTutor2 instanceof Staff && $this->getTutor2() instanceof Staff && $this->inheritedTutor2->getId() === $this->getTutor2()->getId()) {
            $tutor = null;
        }

        $this->setTutor2($tutor);

        $this->local = true;
        return $tutor;
    }

    /**
     * Get Local Tutor3
     *
     * @return Staff|null
     */
    public function getLocalTutor3()
    {
        $this->local = false;

        $this->inheritedTutor3 = $this->getTutor3();
        $tutor = $this->inheritedTutor3;

        $this->setTutor3(null);

        if ($this->inheritedTutor3 instanceof Staff && $this->getTutor3() instanceof Staff && $this->inheritedTutor3->getId() === $this->getTutor3()->getId()) {
            $tutor = null;
        }

        $this->setTutor3($tutor);

        $this->local = true;
        return $tutor;
    }

    /**
     * @return bool
     */
    public function getLocal()
    {
        return $this->local ? true : false;
    }

    /**
     * @param $local
     * @return PeriodActivityModel
     */
    public function setLocal($local)
    {
        $this->local = boolval($local);

        $this->getSpace();
        $this->getTutor1();
        $this->getTutor2();
        $this->getTutor3();

        return $this;
    }

    /**
     * Get Inherited Tutor1
     *
     * @return Space|null
     */
    public function getInheritedTutor1()
    {
        return $this->inheritedTutor1;
    }

    /**
     * Get Inherited Tutor2
     *
     * @return Space|null
     */
    public function getInheritedTutor2()
    {
        return $this->inheritedTutor2;
    }

    /**
     * Get Inherited Tutor3
     *
     * @return Space|null
     */
    public function getInheritedTutor3()
    {
        return $this->inheritedTutor3;
    }

    public function getFullName()
    {
        return $this->getActivity()->getFullName();
    }

    /**
     * @return mixed
     */
    public function getNameShort()
    {
        return $this->getActivity()->getNameShort();
    }

    /**
     * @return Space|null
     */
    public function getSpaceName()
    {
        if ($this->getLocalSpace() instanceof Space)
            return $this->getLocalSpace()->getName();

        return $this->getInheritedSpace()->getName();
    }

    /**
     * Get Local Space
     *
     * @return Space|null
     */
    public function getLocalSpace()
    {
        $this->local = false;

        $this->inheritedSpace = $this->getSpace();
        $space = $this->inheritedSpace;

        $this->setSpace(null);

        if ($this->inheritedSpace instanceof Space && $this->getSpace() instanceof Space && $this->inheritedSpace->getId() === $this->getSpace()->getId()) {
            $space = null;
        }

        $this->setSpace($space);

        $this->local = true;
        return $space;
    }

    /**
     * Get Inherited Space
     *
     * @return Space|null
     */
    public function getInheritedSpace()
    {
        return $this->inheritedSpace;
    }

    /**
     * Get Grades
     *
     * @return null|ArrayCollection
     */
    public function getGrades()
    {
        if (!empty($this->getActivity()))
            return $this->getActivity()->getGrades();
        return null;
    }

    /**
     * @return string
     */
    public function getColumnName(): string
    {
        $period = $this->getPeriod();
        if ($period instanceof Period)
            return $period->getColumnName();
        return '';
    }
}