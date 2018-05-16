<?php
namespace App\Timetable\Form;

use App\Calendar\Util\CalendarManager;
use App\Entity\Activity;
use App\Entity\Calendar;
use App\Entity\Course;
use App\Entity\TimetableLine;
use App\Timetable\Form\Subscriber\LineSubscriber;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityRepository;
use Hillrange\Form\Type\CollectionEntityType;
use Hillrange\Form\Type\CollectionType;
use Hillrange\Form\Type\EntityType;
use Hillrange\Form\Type\HiddenEntityType;
use Hillrange\Form\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LineType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $calendar = $options['calendar_data'];
        $builder
            ->add('name', TextType::class, [
                    'label' => 'line.name.label',
                    'attr' => [
                        'class' => 'monitorChange',
                    ],
                ]
            )
            ->add('code', TextType::class, [
                    'label' => 'line.code.label',
                    'attr' => [
                        'class' => 'monitorChange',
                    ],
                ]
            )
            ->add('activities', CollectionType::class, [
                    'label' => 'line.activities.label',
                    'help' => 'line.activities.help',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'required'  => false,
                    'entry_type' => CollectionEntityType::class,
                    'route' => 'line_remove_activity',
                    'button_merge_class' => 'btn-sm',

                    'entry_options' => [
                        'class' => Activity::class,
                        'block_prefix' => 'line_activity',
                        'choice_label' => 'fullName',
                        'placeholder' => 'line.activities.placeholder',
                        'attr' => [
                            'class' => 'form-control-sm'
                        ],
                        'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('a')
                                ->leftJoin('a.calendarGrades', 'cg')
                                ->leftJoin('cg.calendar', 'c')
                                ->where('c = :calendar')
                                ->setParameter('calendar', CalendarManager::getCurrentCalendar())
                                ->orderBy('a.name', 'ASC')
                                ->andwhere('(a INSTANCE OF :facetoface OR a INSTANCE OF :roll)')
                                ->setParameter('roll', 'roll')
                                ->setParameter('facetoface', 'class')
                                ->orderBy('cg.sequence', 'ASC')
                                ->orderBy('a.name', 'ASC')
                            ;
                        },
                    ],
                ]
            )
            ->add('calendar', HiddenEntityType::class,
                [
                    'class' => Calendar::class,
                ]
            )
            ->add('changeRecord', EntityType::class,
                array(
                    'label' => false,
                    'attr' => array(
                        'class' => 'formChanged changeRecord form-control-sm',
                    ),
                    'class' => TimetableLine::class,
                    'choice_label' => 'name',
                    'mapped' => false,
                    'required' => false,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('l')
                            ->leftJoin('l.calendar', 'c')
                            ->where('c = :calendar')
                            ->setParameter('calendar', CalendarManager::getCurrentCalendar())
                            ->orderBy('l.name', 'ASC');
                    },
                    'placeholder' => 'line.change_record.placeholder',
                )
            );

        $builder->addEventSubscriber(new LineSubscriber());
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => TimetableLine::class,
                'translation_domain' => 'Timetable',
            ]
        );
        $resolver->setRequired(
            [
                'calendar_data',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'line';
    }
}