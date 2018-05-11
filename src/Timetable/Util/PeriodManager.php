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
 * Time: 17:33
 */

namespace App\Timetable\Util;

use App\Core\Manager\MessageManager;
use App\Entity\Space;
use App\Entity\TimetablePeriodActivity;

class PeriodManager
{
    /**
     * @var TimetableManager
     */
    private $timetableManager;

    /**
     * PeriodManager constructor.
     * @param TimetableManager $timetableManager
     */
    public function __construct(TimetableManager $timetableManager)
    {
        $this->timetableManager = $timetableManager;

    }

    /**
     * hasSpace
     *
     * @param TimetablePeriodActivity $activity
     * @return bool
     */
    public function hasSpace(TimetablePeriodActivity $activity): bool
    {
        if ($activity->loadSpace() instanceof Space)
            return true;
        return false;
    }

    /**
     * @param TimetablePeriodActivity $activity
     * @return Space|null
     */
    public function getSpace(TimetablePeriodActivity $activity): ?Space
    {
        return $activity->loadSpace();
    }

    /**
     * @param TimetablePeriodActivity $activity
     * @return bool
     */
    public function hasTutors(TimetablePeriodActivity $activity): bool
    {
        if ($activity->loadTutors()->count() > 0)
            return true;
        return false;
    }

    /**
     * @return MessageManager
     */
    public function getMessageManager(): MessageManager
    {
        return $this->getTimetableManager()->getMessageManager();
    }

    /**
     * @return TimetableManager
     */
    public function getTimetableManager(): TimetableManager
    {
        return $this->timetableManager;
    }
}