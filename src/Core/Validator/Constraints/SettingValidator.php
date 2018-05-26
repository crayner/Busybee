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
 * Time: 13:04
 */
namespace App\Core\Validator\Constraints;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class SettingValidator extends ConstraintValidator
{
    /**
     * @param mixed      $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (empty($value))
            return ;

        switch ($value->getType()){
            case 'array':
                if (is_array($value->getRawValue()))
                    return;
                try {
                    $x = Yaml::parse($value->getRawValue());
                } catch (ParseException $exception) {
                    $this->context->buildViolation('setting.save.error.array')
                        ->setParameter('%{error}', $exception->getMessage())
                        ->setTranslationDomain('Setting')
                        ->atPath('value')
                        ->addViolation();
                    return;
                }
                if (! is_array($x))
                    $this->context->buildViolation('setting.save.error.array')
                        ->setParameter('%{error}', 'Not a valid Yaml string.')
                        ->setTranslationDomain('Setting')
                        ->atPath('value')
                        ->addViolation();
                break;
            default:
                return;
        }
    }
}