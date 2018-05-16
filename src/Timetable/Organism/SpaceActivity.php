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
 * Date: 16/05/2018
 * Time: 16:06
 */

namespace App\Timetable\Organism;

use App\Entity\Space;
use App\Entity\TimetablePeriodActivity;

class SpaceActivity
{

    /**
     * SpaceActivity constructor.
     * @param Space $space
     * @param TimetablePeriodActivity $activity
     */
    public function __construct(Space $space, TimetablePeriodActivity $activity)
    {
        $this->setSpace($space)->setActivity($activity);
    }

    /**
     * @var Space|null
     */
    private $space;

    /**
     * @return Space|null
     */
    public function getSpace(): ?Space
    {
        return $this->space;
    }

    /**
     * @param Space|null $space
     * @return SpaceActivity
     */
    public function setSpace(?Space $space): SpaceActivity
    {
        $this->space = $space;
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
     * @return SpaceActivity
     */
    public function setActivity(?TimetablePeriodActivity $activity): SpaceActivity
    {
        $this->activity = $activity;
        return $this;
    }

    /**
     * __toString
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getSpace()->getNameCapacity() . ' ' . $this->getActivity()->getActivity()->getFullName();
    }
}