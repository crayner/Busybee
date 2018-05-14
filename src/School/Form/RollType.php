<?php
namespace App\School\Form;

use App\Calendar\Util\CalendarManager;
use App\Entity\Activity;
use App\Entity\CalendarGrade;
use App\Entity\Space;
use App\Entity\Staff;
use App\Entity\Student;
use App\School\Form\Subscriber\ActivitySubscriber;
use App\School\Util\ActivityManager;
use Doctrine\ORM\EntityRepository;
use Hillrange\Form\Type\CollectionType;
use Hillrange\Form\Type\EntityType;
use Hillrange\Form\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RollType extends AbstractType
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
     * RollType constructor.
     * @param ActivitySubscriber $activitySubscriber
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

        $grades = [];
        foreach($options['data']->getCalendarGrades()->getIterator() as $grade)
            $grades[] = $grade->getId();
        $stu = new Student();
        $statuses = $stu->getStatusList('active');
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
                    'route' => 'activity_student_manage',
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
                    'query_builder' => function (EntityRepository $er)  {
                        return $er->createQueryBuilder('g')
                            ->leftJoin('g.calendar', 'c')
                            ->where('c = :calendar')
                            ->setParameter('calendar', CalendarManager::getCurrentCalendar())
                            ->orderBy('g.sequence', 'ASC')
                            ;
                    },
                ]
            )
            ->add('attendance', HiddenType::class)
            ->add('reportable', HiddenType::class)
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
                'attr' => [
                    'novalidate' => '',
                    'id' => 'saveForm',
                ],
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
