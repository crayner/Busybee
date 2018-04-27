<?php
namespace App\Timetable\Form;

use App\Core\Type\SettingChoiceType;
use App\Entity\Timetable;
use App\Entity\TimetableDay;
use Hillrange\Form\Type\HiddenEntityType;
use Hillrange\Form\Type\ToggleType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DayEntityType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => TimetableDay::class,
                'translation_domain' => 'Timetable',
                'placeholder' => 'timetable.day.placeholder',
                'class' => TimetableDay::class,
                'choice_label' => 'name',
                'error_bubbling' => true,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'tt_days';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', SettingChoiceType::class,
                [
                    'label' => 'timetable.day.name.label',
                    'setting_name' => 'schoolweek',
                    'placeholder' => 'timetable.day.name.placeholder',
                ]
            )
            ->add('dayType', ToggleType::class,
                [
                    'label' => 'timetable.day.dayType.label',
                    'help' => 'timetable.day.dayType.help',
                    'button_class_off' => 'fas fa-thumbtack btn btn-warning',
                    'button_toggle_swap' =>
                        [
                            'fa-thumbtack',
                            'fa-undo',
                            'btn-primary',
                            'btn-warning',
                        ],
                    'disabled' => $options['disabled'],
                ]
            )
            ->add('timetable', HiddenEntityType::class,
                [
                    'class' => Timetable::class,
                ]
            );

    }
}
