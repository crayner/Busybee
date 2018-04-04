<?php
namespace App\Timetable\Form;

use App\Entity\Timetable;
use App\Entity\TimetableColumn;
use App\Timetable\Validator\Periods;
use Hillrange\Form\Type\CollectionType;
use Hillrange\Form\Type\HiddenEntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ColumnPeriodType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => TimetableColumn::class,
                'translation_domain' => 'Timetable',
                'class' => TimetableColumn::class,
            ]
        );
        $resolver->setRequired(
            [
                'tt_id',
            ]
        );

    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'column_period';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', HiddenType::class)
            ->add('nameShort', HiddenType::class)
            ->add('mappingInfo', HiddenType::class)
            ->add('periods', CollectionType::class,
                [
                    'entry_type' => PeriodType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'constraints' => [
                        new Periods(),
                    ],
                ]
            )
            ->add('timetable', HiddenEntityType::class,
                [
                    'class' => Timetable::class,
                ]
            );
    }
}
