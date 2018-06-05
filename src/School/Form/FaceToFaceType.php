<?php
namespace App\School\Form;

use App\Calendar\Util\CalendarManager;
use App\Entity\Activity;
use App\Entity\CalendarGrade;
use App\Entity\FaceToFace;
use App\Entity\Space;
use App\School\Form\Subscriber\ActivitySubscriber;
use App\School\Util\ActivityManager;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityRepository;
use Hillrange\Form\Type\CollectionType;
use Hillrange\Form\Type\EntityType;
use Hillrange\Form\Type\TextType;
use Hillrange\Form\Type\ToggleType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FaceToFaceType extends AbstractType
{
    /**
     * @var ActivitySubscriber
     */
    private $activitySubscriber;

    /**
     * @var ActivityManager
     */
    private $activityManager;

    /**
     * FaceToFaceType constructor.
     * @param ActivitySubscriber $activitySubscriber
     * @param ActivityManager $activityManager
     */
    public function __construct(ActivitySubscriber $activitySubscriber, ActivityManager $activityManager)
    {
        $this->activitySubscriber = $activitySubscriber;
        $this->activityManager = $activityManager;
    }

    /**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
	    $course = $options['data']->getCourse();
	    $grades = $course->getCalendarGrades()->toArray();
	    foreach($grades as $q=>$w)
	        $grades[$q] = $w->getId();
	    $activities = $course->getActivities()->toArray();
        foreach($activities as $q=>$w)
            $activities[$q] = $w->getId();
        $activity = $options['data']->getId();
        $key = array_search($activity, $activities);
        unset($activities[$key]);
        $add = false;
        if (empty($options['data']->getId()))
            $add = true;
		$builder
            ->add('name', TextType::class,
                [
                    'label' => 'activity.name.label',
                    'help' => 'activity.name.help',
                ]
            )
            ->add('code', TextType::class,
                [
                    'label' => 'activity.code.label',
                    'help' => 'activity.code.help',
                ]
            )
            ->add('useCourseName', ToggleType::class,
                [
                    'label' => 'activity.use_course_name.label',
                    'help' => 'activity.use_course_name.help',
                ]
            )
            ->add('website', UrlType::class,
                [
                    'label' => 'activity.website.label',
                    'required' => false,
                ]
            )
            ->add('space', EntityType::class,
                [
                    'label' => 'activity.space.label',
                    'placeholder' => 'activity.space.placeholder',
                    'class' => Space::class,
                    'choice_label' => 'fullName',
                    'required' => false,
                ]
            )
            ->add('reportable', ToggleType::class,
                [
                    'label' => 'activity.reportable.label',
                ]
            )
            ->add('attendance', ToggleType::class,
                [
                    'label' => 'activity.attendance.label',
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
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('g')
                            ->leftJoin('g.calendar', 'c')
                            ->where('c.id = :calendar_id')
                            ->setParameter('calendar_id', CalendarManager::getCurrentCalendar())
                            ->orderBy('g.sequence', 'ASC')
                            ;
                    },
                ]
            )
        ;
        if (! $add) {
            $builder
                ->add('students', CollectionType::class,
                    [
                        'label' => false,
                        'allow_add' => true,
                        'allow_delete' => true,
                        'entry_type' => ActivityStudentType::class,
                        'required' => false,
                        'entry_options' => [
                            'student_list' => $this->activityManager->getPossibleStudents($options['data']),
                        ],
                        'route' => 'activity_student_manage',
                        'button_merge_class' => 'btn-sm',
                    ]
                )
                ->add('tutors', CollectionType::class,
                    [
                        'entry_type' => ActivityTutorType::class,
                        'allow_add' => true,
                        'allow_delete' => true,
                        'allow_up' => true,
                        'allow_down' => true,
                        'sort_manage' => true,
                        'route' => 'activity_tutor_manage',
                        'button_merge_class' => 'btn-sm',
                    ]
                )
                ->add('studentReference', EntityType::class,
                    [
                        'label' => 'activity.student_reference.label',
                        'help' => 'activity.student_reference.help',
                        'class' => Activity::class,
                        'choice_label' => 'fullName',
                        'placeholder' => 'activity.student_reference.placeholder',
                        'query_builder' => function (EntityRepository $er) use ($grades) {
                            return $er->createQueryBuilder('a')
                                ->leftJoin('a.calendarGrades', 'cg')
                                ->where('cg.id IN (:grades)')
                                ->setParameter('grades', $grades, Connection::PARAM_INT_ARRAY)
                                ->orderBy('a.name', 'ASC')
                                ;
                        },
                    ]
                )

            ;
        }
		$builder->addEventSubscriber($this->activitySubscriber);
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			[
				'data_class'         => FaceToFace::class,
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
