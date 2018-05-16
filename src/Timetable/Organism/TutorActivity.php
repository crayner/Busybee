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
 * Date: 11/05/2018
 * Time: 12:00
 */

namespace App\Timetable\Organism;

use App\Entity\Staff;
use App\Entity\TimetablePeriodActivity;

class TutorActivity
{
    /**
     * @var Staff
     */
    private $tutor;

    public function __construct(Staff $tutor, TimetablePeriodActivity $activity)
    {
        $this->setTutor($tutor)->setActivity($activity);
    }

    /**
     * @return Staff
     */
    public function getTutor(): Staff
    {
        return $this->tutor;
    }

    /**
     * @param Staff $tutor
     * @return TutorActivity
     */
    public function setTutor(Staff $tutor): TutorActivity
    {
        $this->tutor = $tutor;
        return $this;
    }

    /**
     * @var TimetablePeriodActivity
     */
    private $activity;

    /**
     * @return TimetablePeriodActivity
     */
    public function getActivity(): TimetablePeriodActivity
    {
        return $this->activity;
    }

    /**
     * @param TimetablePeriodActivity $activity
     * @return TutorActivity
     */
    public function setActivity(TimetablePeriodActivity $activity): TutorActivity
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
        return $this->getActivity()->getFullName() . ' ' . $this->getTutor()->getFullName();
    }
}