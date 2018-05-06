<?php
namespace App\Timetable\Form;

use App\Timetable\Organism\DayDate;
use Hillrange\Form\Type\CollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DayDateType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('timetableId', HiddenType::class)
            ->add('terms', CollectionType::class,
                [
                    'entry_type' => TermType::class,
                ]
            )
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => DayDate::class,
                'translation_domain' => 'Timetable'
            ]
        );
    }
}