<?php
namespace App\School\Form;

use App\Entity\ActivityStudent;
use App\Entity\ActivityTutor;
use App\Entity\FaceToFace;
use App\Entity\Space;
use App\School\Form\Subscriber\ActivitySubscriber;
use Doctrine\ORM\EntityManagerInterface;
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
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * ActivityType constructor.
     * @param ActivitySubscriber $activitySubscriber
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(ActivitySubscriber $activitySubscriber, EntityManagerInterface $entityManager)
    {
        $this->activitySubscriber = $activitySubscriber;
        $this->entityManager = $entityManager;
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
            ->add('nameShort', TextType::class,
                [
                    'label' => 'activity.name_short.label',
                    'help' => 'activity.name_short.help',
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
                    'attr' => [
                        'class' => 'studentCollection'
                    ],
                    'required' => false,
                    'remove_manage' => true,
                    'entity_class' => ActivityStudent::class,
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
                    'sequence_manage' => true,
                    'remove_manage' => true,
                    'entity_class' => ActivityTutor::class,
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
