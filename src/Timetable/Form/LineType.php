<?php
namespace App\Timetable\Form;

use App\Entity\Calendar;
use App\Entity\Course;
use App\Entity\Line;
use App\Timetable\Form\Subscriber\LineSubscriber;
use Doctrine\ORM\EntityRepository;
use Hillrange\Form\Type\EntityType;
use Hillrange\Form\Type\HiddenEntityType;
use Hillrange\Form\Type\TextType;
use Hillrange\Form\Type\ToggleType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
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
            ->add('participants', NumberType::class, [
                    'label' => 'line.participants.label',
                    'help' => 'line.participants.help',
                    'attr' => [
                        'class' => 'monitorChange',
                    ],
                    'required' => false,
                    'empty_data' => 0,
                ]
            )
            ->add('includeAll', ToggleType::class, [
                    'label' => 'line.include_all.label',
                    'help' => 'line.include_all.help',
                    'attr' => [
                        'class' => 'monitorChange',
                    ],
                ]
            )
            ->add('course', EntityType::class, [
                    'class' => Course::class,
                    'choice_label' => 'name',
                    'placeholder' => 'line.course.placeholder',
                    'label' => 'line.course.label',
                    'required' => false,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                            ->orderBy('c.name', 'ASC');
                    },
                    'attr' => [
                        'class' => 'monitorChange',
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
                    'class' => Line::class,
                    'choice_label' => 'name',
                    'mapped' => false,
                    'required' => false,
                    'query_builder' => function (EntityRepository $er) use ($calendar) {
                        return $er->createQueryBuilder('l')
                            ->leftJoin('l.calendar', 'c')
                            ->where('c.id = :calendar_id')
                            ->setParameter('calendar_id', $calendar->getId())
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
                'data_class' => Line::class,
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