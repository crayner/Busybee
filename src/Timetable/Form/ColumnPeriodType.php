<?php
namespace App\Timetable\Form;

use App\Core\Type\SettingChoiceType;
use App\Entity\TimetableColumn;
use App\Entity\TimetableColumnPeriod;
use Hillrange\Form\Type\HiddenEntityType;
use Hillrange\Form\Type\TextType;
use Hillrange\Form\Type\TimeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ColumnPeriodType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class,
                [
                    'attr' => [
                        'placeholder' => 'timetable.column.period.name.placeholder',
                    ],
                ]
            )
            ->add('code', TextType::class,
                [
                    'label' => 'timetable.column.period.code.label',
                ]
            )
            ->add('type', SettingChoiceType::class,
                [
                    'label' => 'timetable.column.period.type.label',
                    'placeholder' => 'timetable.column.period.type.placeholder',
                    'setting_name' => 'period.type.list',
                    'translation_prefix' => true,
                ]
            )
            ->add('timeStart', TimeType::class,
                [
                    'label' => 'timetable.column.period.time_start.label',
                ]
            )
            ->add('timeEnd', TimeType::class,
                [
                    'label' => 'timetable.column.period.time_end.label',
                ]
            )
            ->add('column', HiddenEntityType::class,
                [
                    'class' => TimetableColumn::class,
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'translation_domain' => 'Timetable',
                'data_class' => TimetableColumnPeriod::class,
            ]
        );
    }

    public function getBlockPrefix()
    {
        return 'tt_column_period';
    }
}