<?php
namespace App\School\Form;

use App\Calendar\Util\CalendarManager;
use App\Core\Subscriber\SequenceSubscriber;
use App\Core\Type\SettingChoiceType;
use App\Entity\Activity;
use App\Entity\CalendarGrade;
use App\Entity\Student;
use App\Entity\Term;
use App\People\Util\StudentManager;
use App\Repository\StudentRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityRepository;
use Hillrange\CKEditor\Form\CKEditorType;
use Hillrange\Form\Type\EntityType;
use Hillrange\Form\Type\TextType;
use Hillrange\Form\Type\ToggleType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExternalActivityType extends AbstractType
{
    /**
     * @var StudentManager
     */
    private $studentManager;

    /**
     * ExternalActivityType constructor.
     * @param StudentManager $studentManager
     */
    public function __construct(StudentManager $studentManager)
    {
        $this->studentManager = $studentManager;
    }

    /**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
        $grades = $options['data']->getCalendarGrades()->toArray();
        foreach($grades as $q=>$w)
            $grades[$q] = $w->getId();

        $currentCalendar = $this->studentManager->getCalendarManager()->getCurrentCalendar();

		$builder
            ->add('name', TextType::class,
                [
                    'label' => 'external_activity.name.label',
                    'help' => 'external_activity.name.help',
                ]
            )
            ->add('students', CollectionType::class,
                [
                    'label' => 'external_activity.students.label',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'attr' => [
                        'class' => 'studentCollection',
                    ],
                    'entry_type' => ExternalActivityStudentsType::class,
                    'entry_options' => [
                        'student_list' => $this->studentManager->generateStudentList($options['data']->getCalendarGrades()),
                    ],
                ]
            )
            ->add('provider', SettingChoiceType::class,
                [
                    'label' => 'external_activity.provider.label',
                    'setting_name' => 'activity.provider.type',
                    'placeholder' => 'external_activity.provider.placeholder',
                ]
            )
            ->add('tutors', CollectionType::class,
                [
                    'entry_type' => ActivityTutorType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'attr' => [
                        'class' => 'tutorCollection'
                    ],
                ]
            )
            ->add('registration', ToggleType::class,
                [
                    'label' => 'external_activity.registration.label',
                    'help' => 'external_activity.registration.help',
                ]
            )
            ->add('active', ToggleType::class,
                [
                    'label' => 'external_activity.active.label',
                ]
            )
            ->add('terms', EntityType::class,
                [
                    'label' => 'external_activity.terms.label',
                    'help' => 'external_activity.terms.help',
                    'class' => Term::class,
                    'choice_label' => 'name',
                    'required' => false,
                    'multiple' => true,
                    'expanded' => true,
                    'attr' => [
                        'class' => 'form-control-sm',
                    ],
                    'query_builder' => function (EntityRepository $er) use ($currentCalendar) {
                        return $er->createQueryBuilder('t')
                            ->leftJoin('t.calendar', 'c')
                            ->where('c.id = :calendar_id')
                            ->setParameter('calendar_id', $currentCalendar->getId())
                            ->orderBy('t.firstDay', 'ASC')
                        ;
                    },
                ]
            )
            ->add('calendarGrades', EntityType::class,
                [
                    'label' => 'external_activity.calendar_grade.label',
                    'help' => 'external_activity.calendar_grade.help',
                    'class' => CalendarGrade::class,
                    'choice_label' => 'grade',
                    'required' => false,
                    'multiple' => true,
                    'expanded' => true,
                    'attr' => [
                        'class' => 'form-control-sm',
                    ],
                    'query_builder' => function (EntityRepository $er) use ($currentCalendar) {
                        return $er->createQueryBuilder('g')
                            ->leftJoin('g.calendar', 'c')
                            ->where('c.id = :calendar_id')
                            ->setParameter('calendar_id', $currentCalendar->getId())
                            ->orderBy('g.sequence', 'ASC')
                            ;
                    },
                ]
            )
            ->add('maxParticipants', NumberType::class,
                [
                    'label' => 'external_activity.max_participants.label',
                ]
            )
            ->add('description', CKEditorType::class,
                [
                    'label' => 'external_activity.description.label',
                    'attr'     => [
                        'rows' => 4,
                    ],
                    'required' => false,
                ]
            )
            ->add('payment', NumberType::class,
                [
                    'label' => 'external_activity.payment.label',
                    'scale' => 2,
                ]
            )
            ->add('paymentType', SettingChoiceType::class,
                [
                    'label' => 'external_activity.payment_type.label',
                    'setting_name' => 'activity.payment.type',
                ]
            )
            ->add('paymentFirmness', SettingChoiceType::class,
                [
                    'label' => 'external_activity.payment_firmness.label',
                    'setting_name' => 'activity.payment.firmness',
                ]
            )
        ;
        $builder->get('tutors')->addEventSubscriber(new SequenceSubscriber());
//		$builder->addEventSubscriber($this->activitySubscriber);
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			[
				'data_class'         => Activity::class,
				'translation_domain' => 'School',
			]
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'activity';
	}
}
