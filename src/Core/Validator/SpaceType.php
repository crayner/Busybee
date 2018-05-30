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
 * Date: 30/05/2018
 * Time: 09:10
 */
namespace App\Core\Validator;

use App\Core\Validator\Constraints\SpaceTypeValidator;
use Symfony\Component\Validator\Constraint;

class SpaceType extends Constraint
{
    /**
     * @var array
     */
    public $required = [
        'teaching_space',
        'non_teaching_space',
    ];

    /**
     * @var string
     */
    public $transDomain = 'Setting';

    /**
     * validatedBy
     *
     * @return string
     */
    public function validatedBy()
    {
        return SpaceTypeValidator::class;
    }
}