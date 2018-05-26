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
 * Date: 25/05/2018
 * Time: 13:03
 */
namespace App\Core\Validator;

use App\Core\Validator\Constraints\SettingValidator;
use Symfony\Component\Validator\Constraint;

class Setting extends Constraint
{
    /**
     * @return string
     */
    public function validatedBy()
    {
        return SettingValidator::class;
    }

    /**
     * @return array
     */
    public function getTargets()
    {
        return [self::CLASS_CONSTRAINT];
    }
}