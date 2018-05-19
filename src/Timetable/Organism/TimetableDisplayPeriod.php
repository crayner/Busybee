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
 * Date: 19/05/2018
 * Time: 09:18
 */

namespace App\Timetable\Organism;

use App\Entity\TimetablePeriod;
use App\Entity\TimetablePeriodActivity;

class TimetableDisplayPeriod
{
    /**
     * TimetableDisplayPeriod constructor.
     * @param TimetablePeriod $period
     */
    public function __construct(TimetablePeriod $period)
    {
        $this->setPeriod($period);
    }

    /**
     * @var TimetablePeriod
     */
    private $period;

    /**
     * @return TimetablePeriod
     */
    public function getPeriod(): TimetablePeriod
    {
        return $this->period;
    }

    /**
     * @param TimetablePeriod $period
     * @return TimetableDisplayPeriod
     */
    public function setPeriod(TimetablePeriod $period): TimetableDisplayPeriod
    {
        $this->period = $period;
        return $this;
    }

    /**
     * @var TimetablePeriodActivity|null
     */
    private $activity;

    /**
     * @return TimetablePeriodActivity|null
     */
    public function getActivity(): ?TimetablePeriodActivity
    {
        return $this->activity;
    }

    /**
     * @param TimetablePeriodActivity|null $activity
     * @return TimetableDisplayPeriod
     */
    public function setActivity(?TimetablePeriodActivity $activity): TimetableDisplayPeriod
    {
        $this->activity = $activity;
        return $this;
    }

    /**
     * isBreak
     *
     * @return bool
     */
    public function isBreak(): bool
    {
        return $this->getPeriod()->isBreak();
    }

    /**
     * isActive
     *
     * @return bool
     */
    public function isActive(): bool
    {
        if ($this->getActivity())
            return true;
        return false;
    }

    /**
     * @var string|null
     */
    private $class;

    /**
     * @return null|string
     */
    public function getClass(): ?string
    {
        return $this->class;
    }

    /**
     * @param null|string $class
     * @return TimetableDisplayPeriod
     */
    public function setClass(?string $class): TimetableDisplayPeriod
    {
        $this->class = $class;
        return $this;
    }

    /**
     * getMinutes
     *
     * @return int
     */
    public function getMinutes(): int
    {
        return $this->getPeriod()->getMinutes();
    }

    /**
     * getName
     *
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->getPeriod()->getName();
    }

    /**
     * getStart
     *
     * @return \DateTime|null
     */
    public function getStart(): ?\DateTime
    {
        return $this->getPeriod()->getStart();
    }
    public function getEnd(): ?\DateTime
    {
        return $this->getPeriod()->getEnd();
    }
}