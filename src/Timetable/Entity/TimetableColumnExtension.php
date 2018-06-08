<?php
namespace App\Timetable\Entity;

use App\Entity\TimetableColumn;
use Hillrange\Security\Util\UserTrackInterface;
use Hillrange\Security\Util\UserTrackTrait;

abstract class TimetableColumnExtension implements UserTrackInterface
{
    use UserTrackTrait;

    /**
     * @return bool
     */
    public function canDelete()
    {
        if ($this->getPeriods()->count() > 0)
            return false;

        return true;
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return $this->getName() . ' (' . $this->getCode() . ')';
    }

    /**
     * isEqualTo
     *
     * @param TimetableColumn|null $column
     * @return bool
     */
    public function isEqualTo(?TimetableColumn $column): bool
    {
        if ($this->getId() === $column->getId())
            return true;

        return false;
    }
}