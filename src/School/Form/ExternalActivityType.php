<?php
namespace App\School\Form;

use App\Core\Subscriber\SequenceSubscriber;
use App\Core\Type\SettingChoiceType;
use App\Entity\Activity;
use App\Entity\ActivitySlot;
use App\Entity\ActivityStudent;
use App\Entity\CalendarGrade;
use App\Entity\Staff;
use App\Entity\Student;
use App\Entity\Term;
use App\People\Util\StudentManager;
use App\School\Form\Subscriber\ActivitySubscriber;
use Doctrine\ORM\EntityRepository;
use Hillrange\CKEditor\Form\CKEditorType;
use Hillrange\Form\Type\CollectionType;
use Hillrange\Form\Type\EntityType;
use Hillrange\Form\Type\TextType;
use Hillrange\Form\Type\ToggleType;
use Symfony\Component\Form\AbstractType;
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
     * @var ActivitySubscriber
     */
    private $activitySubscriber;
    /**
     * ExternalActivityType constructor.
     * @param StudentManager $studentManager
     */
    public function __construct(StudentManager $studentManager, ActivitySubscriber $activitySubscriber)
    {
        $this->studentManager = $studentManager;
        $this->activitySubscriber = $activitySubscriber;
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
            ->add('nameShort', TextType::class,
                [
                    'label' => 'external_activity.name_short.label',
                    'help' => 'external_activity.name_short.help',
                ]
            )
            ->add('students', CollectionType::class,
                [
                    'label' => 'external_activity.students.label',
                    'allow_add' => true,
                    'allow_delete' => true,
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
                    'allow_up' => true,
                    'allow_down' => true,
                    'sequence_manage' => true,
                ]
            )
            ->add('registration', ToggleType::class,
                [
                    'label' => 'external_activity.registration.label',
                    'help' => 'external_activity.registration.help',
                ]
            )
            ->add('type', SettingChoiceType::class,
                [
                    'label' => 'external_activity.type.label',
                    'placeholder' => 'external_activity.type.placeholder',
                    'setting_name' => 'external.activity.type.list',
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
            ->add('activitySlots', CollectionType::class,
                [
                    'label' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'entry_type' => ActivitySlotType::class,
                ]
            )
        ;
		$builder->addEventSubscriber($this->activitySubscriber);
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
