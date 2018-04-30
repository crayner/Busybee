<?php
namespace App\School\Form;

use App\Entity\FaceToFace;
use App\Entity\Space;
use App\School\Form\Subscriber\ActivitySubscriber;
use App\School\Util\ActivityManager;
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
            ->add('students', CollectionType::class,
                [
                    'label' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'entry_type' => ClassStudentType::class,
                    'required' => false,
                    'entry_options' => [
                        'student_list' => $this->activityManager->generatePossibleStudents($options['data']),
                    ],
                    'route' => 'class_student_manage',
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
                    'route' => 'class_tutor_manage',
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
