<?php
namespace App\Timetable\Entity;

use App\Entity\TimetablePeriod;
use Hillrange\Security\Util\UserTrackInterface;
use Hillrange\Security\Util\UserTrackTrait;

abstract class TimetablePeriodExtension implements UserTrackInterface
{
    use UserTrackTrait;
    /**
     * @return bool
     * @todo    Build canDelete Test for Period
     */
    public function canDelete()
    {
        return false;
    }

    /**
     * @return mixed
     */
    public function getColumnName()
    {
        if (is_null($this->getColumn()))
            return '';
        return $this->getColumn()->getName() . ' - ' . $this->getFullName();
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->getName() . ' (' . $this->getCode() . ')';
    }

    /**
     * @return string
     */
    public function getStartTime($format = 'H:i')
    {
        return $this->getStart()->format($format);
    }

    /**
     * @return string
     */
    public function getEndTime($format = 'H:i')
    {
        return $this->getEnd()->format($format);
    }

    public function getTimeTable()
    {
        $col = $this->getColumn();
        if (is_null($col))
            return null;

        return $col->getTimeTable();
    }

    /**
     * Get Minutes (interval)
     *
     * @return int
     */
    public function getMinutes()
    {
        $interval = ($this->getEnd()->getTimeStamp() - $this->getStart()->getTimeStamp()) / 60;

        return $interval;
    }

    /**
     * @return array
     */
    public function getPeriodTypeList(string $limit = '')
    {
        if (! in_array($limit, ['no students','students','','flat']))
            throw new \InvalidArgumentException('Dear Programmer: The list accepts only [no students, students] for Period Type List');

        $x =  [
            'students' => [
                'class',
                'pastoral',
            ],
            'no students' => [
                'break',
                'meeting',
            ],
        ];

        if ($limit === 'no students')
            return $x['no students'];
        if ($limit === 'students')
            return $x['students'];
        if ($limit === 'flat')
            return array_merge($x['students'], $x['no students']);
        return $x;
    }

    /**
     * @param TimetablePeriod|null $period
     * @return bool
     */
    public function isEqualTo(?TimetablePeriod $period): bool
    {
        if ($this !== $period)
            return false;

        if ($this->getLastModified() !== $period->getLastModified())
            return false;

        return true;
    }
}