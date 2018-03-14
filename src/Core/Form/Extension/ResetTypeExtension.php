<?php
namespace App\Core\Form\Extension;

use App\Core\Validator\ResetSet;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GroupSequence;

class ResetTypeExtension extends AbstractTypeExtension
{
    /**
     * @return string
     */
    public function getExtendedType()
    {
        return FormType::class;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'constraints' => [
                new ResetSet(['groups' => ['resetOnly']]),
            ],
            'validation_groups' => new GroupSequence(['resetOnly', 'default']),
        ]);
    }
}