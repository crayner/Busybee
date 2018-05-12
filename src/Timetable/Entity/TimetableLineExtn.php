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
 * Date: 12/05/2018
 * Time: 11:52
 */

namespace App\Timetable\Entity;

abstract class TimetableLineExtn
{
    /**
     * getActivityNames
     *
     * @return string
     */
    public function getActivityNames(): string
    {
        $x = '';
        foreach($this->getActivities()->getIterator() as $activity)
            $x .= $activity->getFullName() . ', ';

        return trim($x, ', ');
    }
}