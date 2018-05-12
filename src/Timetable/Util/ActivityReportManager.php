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
 * Date: 9/05/2018
 * Time: 16:56
 */

namespace App\Timetable\Util;

use App\Core\Util\ReportManager;
use App\Entity\Space;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class ActivityReportManager extends ReportManager
{
    /**
     * @var ArrayCollection
     */
    private $grades;

    /**
     * getGrades
     *
     * @return ArrayCollection
     */
    public function getGrades(): ArrayCollection
    {
        if (empty($this->grades))
            throw new \InvalidArgumentException('The grades need to be injected into the report.');
        return $this->grades;
    }

    /**
     * setGrades
     *
     * @param ArrayCollection $grades
     * @return ActivityReportManager
     */
    public function setGrades(ArrayCollection $grades): ActivityReportManager
    {
        if ($this->grades !== $grades)
            $this->setRefreshReport(true);
        $this->grades = $grades;
        return $this;
    }

    /**
     * getAllocatedStudents
     *
     * @return Collection
     */
    public function getAllocatedStudents(): Collection
    {
        if (! $this->isActivityActive())
            return new ArrayCollection();
        return $this->getEntity()->getActivity()->getStudents();
    }

    /**
     * @var bool
     */
    private $activityActive;

    /**
     * @return bool
     */
    public function isActivityActive(): bool
    {
        if (!is_null($this->activityActive))
            return $this->activityActive;

        $this->activityActive = false;

        foreach($this->getEntity()->getActivity()->getCalendarGrades() as $grade)
            if ($this->getGrades()->contains($grade)) {
                $this->activityActive = true;
                break;
            }
        return $this->activityActive;
    }

    /**
     * hasSpace
     *
     * @return bool
     */
    public function hasSpace(): bool
    {
        if ($this->getEntity()->loadSpace() instanceof Space)
            return true;
        return false;
    }

    /**
     * hasTutors
     *
     * @return bool
     */
    public function hasTutors(): bool
    {
        if ($this->getEntity()->loadTutors()->count() > 0)
            return true;
        return false;
    }
}