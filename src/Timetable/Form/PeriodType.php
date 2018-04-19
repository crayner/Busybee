<?php
namespace App\Timetable\Form;

use App\Entity\TimetableColumn;
use App\Entity\TimetablePeriod;
use Hillrange\Form\Type\HiddenEntityType;
use Hillrange\Form\Type\TimeType;
use Hillrange\Form\Type\ToggleType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PeriodType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null,
                [
                    'label' => 'period.name.label',
                ]
            )
            ->add('nameShort', null,
                [
                    'label' => 'period.nameShort.label',
                    'attr' =>
                        [
                            'help' => 'period.nameShort.help',
                        ],
                ]
            )
            ->add('start', TimeType::class,
                [
                    'with_seconds' => false,
                    'label' => 'period.start.label',
                ]
            )
            ->add('end', TimeType::class,
                [
                    'with_seconds' => false,
                    'label' => 'period.end.label',
                ]
            )
            ->add('break', ToggleType::class,
                [
                    'label' => 'period.break.label',
                ]
            )
            ->add('column', HiddenEntityType::class,
                [
                    'class' => TimetableColumn::class,
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => TimetablePeriod::class,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'tt_period';
    }


}
