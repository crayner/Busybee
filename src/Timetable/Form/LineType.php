<?php
namespace App\Timetable\Form;

use App\Entity\Calendar;
use App\Entity\Course;
use App\Entity\TimetableLine;
use App\Timetable\Form\Subscriber\LineSubscriber;
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
            ->add('courses', CollectionType::class, [
                    'label' => 'line.courses.label',
                    'help' => 'line.courses.help',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'required'  => false,
                    'entry_type' => CollectionEntityType::class,
                    'route' => 'line_remove_course',
                    'entry_options' => [
                        'class' => Course::class,
                        'block_prefix' => 'line_course',
                        'choice_label' => 'name',
                        'placeholder' => 'line.courses.placeholder'
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