<?php
/**
 * Created by PhpStorm.
 * User: craig
 * Date: 10/03/2018
 * Time: 03:57
 */

namespace App\School\Entity;


class ActivityTutorExtension
{
    /**
     * @return bool
     * @todo Activity Tutor Can Delete
     */
    public function canDelete(): bool
    {
        return true;
    }
}