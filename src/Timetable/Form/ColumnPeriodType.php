<?php
namespace App\Timetable\Form;

use App\Entity\TimetableColumn;
use App\Entity\TimetablePeriod;
use Hillrange\Form\Type\EnumType;
use Hillrange\Form\Type\HiddenEntityType;
use Hillrange\Form\Type\TextType;
use Hillrange\Form\Type\TimeType;
use Symfony\Component\Form\AbstractType;
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
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'timetable_column_period';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [])
            ->add('code', TextType::class, [])
            ->add('periodType', EnumType::class,
                [
                    'choice_list_class' => TimetablePeriod::class,
                    'choice_list_method' => 'getPeriodTypeList',
                    'choice_list_prefix' => 'column.period.period_type',
                ]
            )
            ->add('start', TimeType::class, [])
            ->add('end', TimeType::class, [])
            ->add('column', HiddenEntityType::class,
                [
                    'class' => TimetableColumn::class,
                ]
            )
        ;
    }
}
