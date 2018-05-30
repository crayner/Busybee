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
 * Time: 09:13
 */
namespace App\Core\Validator\Constraints;

use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class SettingKeyRequiredValidator extends ConstraintValidator
{
    /**
     * validate
     *
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (empty($value))
            return $value;
        $resolver = new OptionsResolver();
        $resolver->setRequired($constraint->required);

        try {
            $options = Yaml::parse($value);
        } catch (ParseException $e) {
            $this->context->buildViolation('yaml.validation.error')
                ->setParameter('%systemMessage%', $e->getMessage())
                ->setTranslationDomain('Setting')
                ->addViolation();
            return $value;
        }

        try {
            $newValue = $resolver->resolve($options);
        } catch (UndefinedOptionsException $e) {
            foreach($options as $option=>$values)
            {
                if (! $resolver->isDefined($option))
                    $this->context->buildViolation('setting.option.extra')
                        ->setParameter('%{option}', $option)
                        ->setParameter('%{defined}', implode(', ', $resolver->getDefinedOptions()))
                        ->setTranslationDomain('Setting')
                        ->addViolation();
            }
        } catch (MissingOptionsException $e) {
            foreach($resolver->getRequiredOptions() as $option)
            {
                if (! isset($options[$option])) {
                    $this->context->buildViolation('setting.option.missing')
                        ->setParameter('%{option}', $option)
                        ->setParameter('%{defined}', implode(', ', $resolver->getRequiredOptions()))
                        ->setTranslationDomain('Setting')
                        ->addViolation();
                }
            }
        }

        if (isset($newArray) && is_array($newValue))
            return Yaml::dump($newValue);
        return $value;
    }
}