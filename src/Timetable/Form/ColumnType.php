<?php
namespace App\Timetable\Form;

use App\Entity\TimetableColumn;
use Hillrange\Form\Type\CollectionType;
use Hillrange\Form\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ColumnType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class,
                [
                    'label' => 'timetable.column.name.label',
                ]
            )
            ->add('code', TextType::class,
                [
                    'label' => 'timetable.column.code.label',
                ]
            )
            ->add('periods', CollectionType::class,
                [
                    'entry_type' => ColumnPeriodType::class,
                    'label' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                ]
            )
        ;
    }

    public function getBlockPrefix()
    {
        return 'tt_column';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'translation_domain' => 'Timetable',
                'data_class' => TimetableColumn::class,
            ]
        );
    }
}