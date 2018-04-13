<?php
namespace App\Collection\Form;

use App\Collection\Organism\Test;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CollectionTestType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $values = $options['data']->getValues();
        $w = [];
        foreach($values->getIterator() as $x)
            $w[$x->getName()] = $x->getName();

        $builder
            ->add('values', CollectionType::class,
                [
                    'entry_type' => ChoiceType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'entry_options' => [
                        'choices' => $w,
                    ],
                ]
            )
            ->add('submit', SubmitType::class)
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Test::class,
            ]
        );
    }
}