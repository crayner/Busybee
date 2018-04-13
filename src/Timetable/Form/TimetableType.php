<?php
namespace App\Timetable\Form;

use App\Entity\Calendar;
use App\Entity\Timetable;
use App\Timetable\Form\Subscriber\TimetableSubscriber;
use Hillrange\Form\Type\CollectionType;
use Hillrange\Form\Type\EntityType;
use Hillrange\Form\Type\ToggleType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TimetableType extends AbstractType
{
    /**
     * @var TimetableSubscriber
     */
    private $timetableSubscriber;

    /**
     * TimetableType constructor.
     * @param TimetableSubscriber $timetableSubscriber
     */
    public function __construct(TimetableSubscriber $timetableSubscriber)
    {
        $this->timetableSubscriber = $timetableSubscriber;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $locked = $options['data']->isLocked();
        $builder
            ->add('name', null,
                [
                    'label'    => 'timetable.name.label',
                    'disabled' => $locked,
                ]
            )
            ->add('code', null,
                [
                    'label'    => 'timetable.code.label',
                    'disabled' => $locked,
                ]
            )
            ->add('calendar', EntityType::class,
                [
                    'label'         => 'timetable.calendar.label',
                    'placeholder'   => 'timetable.calendar.placeholder',
                    'disabled'      => $locked,
                    'class'         => Calendar::class,
                    'choice_label'  => 'name',
                ]
            )
            ->add('locked', ToggleType::class,
                [
                    'label'     => 'timetable.locked.label',
                    'help'      => 'timetable.locked.help',
                    'disabled'  => $locked,
                    'button_class_off' => "btn btn-info fas fa-lock-open",
                    'button_toggle_swap' => [
                        'btn-info',
                        'btn-primary',
                        'fa-lock-open',
                        'fa-lock',
                    ],
                ]
            )
            ->add('days', CollectionType::class,
                [
                    'entry_type'    => DayEntityType::class,
                    'help'          => 'timetable.days.help',
                    'label'         => 'timetable.days.label',
                    'allow_delete'  => false,
                    'allow_add'     => false,
                    'disabled'      => $locked,
                ]
            )
        ;
        if ($locked)
            $builder
                ->add('columns', CollectionType::class,
                    [
                        'entry_type'    => ColumnEntityType::class,
                        'help'          => 'timetable.columns.help',
                        'label'         => 'timetable.columns.label',
                        'allow_delete'  => false,
                        'allow_add'     => false,
                        'entry_options' =>
                        [
                            'timetable_id'  => $options['data']->getId(),
                        ],
                        'disabled'      => $locked,
                        'sort_manage'   => true,
                        'route'         => 'timetable_days_edit',
                    ]
                )
            ;
        else
            $builder
                ->add('columns', CollectionType::class,
                    [
                        'entry_type'    => ColumnEntityType::class,
                        'help'  => 'timetable.columns.help',
                        'label'         => 'timetable.columns.label',
                        'allow_delete'  => true,
                        'allow_add'     => true,
                        'entry_options' =>
                        [
                            'timetable_id'  => $options['data']->getId(),
                        ],
                        'disabled'      => $locked,
                        'sort_manage'   => true,
                        'route'         => 'timetable_days_edit',
                    ]
                )
            ;

        $builder->addEventSubscriber($this->timetableSubscriber);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class'         => Timetable::class,
                'translation_domain' => "Timetable",
                'attr' => [
                    'novalidate' => '',
                ],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'timetable';
    }
}