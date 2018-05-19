<?php
/**
 * Created by PhpStorm.
 *
 * This file is part of the Busybee Project.
 *
 * (c) Craig Rayner <craig@craigrayner.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * User: craig
 * Date: 18/05/2018
 * Time: 14:34
 */

namespace App\Timetable\Organism;

use App\Entity\TimetableAssignedDay;
use App\Entity\TimetablePeriod;
use App\Entity\TimetablePeriodActivity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class TimetableDay
{
    /**
     * @var \DateTime|null
     */
    private $date;

    /**
     * @return \DateTime|null
     */
    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     * @return TimetableDay
     */
    public function setDate(?\DateTime $date): TimetableDay
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @var TimetableAssignedDay
     */
    private $day;

    /**
     * @return TimetableAssignedDay
     */
    public function getDay(): TimetableAssignedDay
    {
        return $this->day;
    }

    /**
     * @param TimetableAssignedDay $ttDay
     * @return TimetableDay
     */
    public function setDay(TimetableAssignedDay $day): TimetableDay
    {
        $this->day = $day;
        return $this;
    }

    /**
     * @var Collection
     */
    private $periods;

    /**
     * get Periods
     *
     * @return Collection
     */
    public function getPeriods(): Collection
    {
        if (empty($this->periods))
            $this->periods = new ArrayCollection();

        return $this->periods;
    }

    /**
     * addPeriod
     *
     * @param TimetableDisplayPeriod|null $period
     * @return TimetableDay
     */
    public function addPeriod(?TimetableDisplayPeriod $period): TimetableDay
    {
        if (empty($period) || $this->getPeriods()->contains($period))
            return $this;

        $this->getPeriods()->add($period);

        return $this;
    }

    /**
     * setPeriodActivity
     *
     * @param TimetablePeriod $period
     * @param TimetablePeriodActivity $activity
     * @return TimetableDay
     */
    public function setPeriodActivity(TimetablePeriod $period, TimetablePeriodActivity $activity): TimetableDay
    {
        foreach($this->getPeriods() as $dayPeriod)
            if ($dayPeriod->getPeriod()->isEqualTo($period))
                break;

        if (empty($dayPeriod))
            return $this;

        $dayPeriod->setActivity($activity);

        return $this;
    }
}