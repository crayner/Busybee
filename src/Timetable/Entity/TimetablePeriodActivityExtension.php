<?php
namespace App\Timetable\Entity;

use App\Entity\Space;
use App\Entity\TimetablePeriod;
use App\Entity\TimetablePeriodActivity;
use Doctrine\Common\Collections\Collection;
use Hillrange\Security\Util\UserTrackInterface;
use Hillrange\Security\Util\UserTrackTrait;

abstract class TimetablePeriodActivityExtension implements UserTrackInterface
{
    use UserTrackTrait;

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return $this->getActivity()->getFullName();
    }

    /**
     * @return mixed
     */
    public function getcode()
    {
        return $this->getActivity()->getcode();
    }

    /**
     * Get Grades
     *
     * @return null|Collection
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
        if ($period instanceof TimetablePeriod)
            return $period->getColumnName();
        return '';
    }

    /**
     * @return Collection
     */
    public function loadTutors(): Collection
    {
        $tutors = $this->getTutors();

        if ($tutors->count() === 0)
            $tutors = $this->getActivity()->getTutors();

        return $tutors;
    }

    /**
     * @return Space|null
     */
    public function loadSpace(): ?Space
    {
        if ($this->getSpace() instanceof Space)
            return $this->getSpace();

        return $this->getActivity()->getSpace();

    }

    /**
     * @return string
     */
    public function loadTutorNames(): string
    {
        $names = '';
        foreach($this->loadTutors()->getIterator() as $tutor)
            $names .= $tutor->getTutor()->getFullName(['preferredOnly' => true]) . ', ';

        return trim($names, ', ');
    }

    /**
     * @return bool
     */
    public function isCapacityExceeded(): bool
    {
        if (empty($this->loadSpace()->getCapacity()))
            return false;
        if ($this->getActivity()->getStudents()->count() > $this->loadSpace()->getCapacity())
            return true;
        return false;
    }

    public function hasSpace(): bool
    {

    }

    /**
     * @var string
     */
    private $status = 'default';

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return bool
     */
    public function hasTutors(): bool
    {
        if ($this->loadTutors()->count() > 0)
            return true;
        return false;
    }

    /**
     * @param TimetablePeriodActivity|null $activity
     * @return bool
     */
    public function isEqualTo(?TimetablePeriodActivity $activity): bool
    {
        if ($activity !== $this)
            return false;

        if ($activity->getLastModified() !== $this->getLastModified())
            return false;

        return true;
    }
}