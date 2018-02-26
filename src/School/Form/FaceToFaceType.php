<?php
namespace App\School\Form;

use App\Core\Subscriber\SequenceSubscriber;
use App\Entity\Activity;
use App\Entity\ActivityTutor;
use App\Entity\Space;
use App\Entity\Staff;
use App\Entity\Student;
use App\Repository\StudentRepository;
use App\School\Form\Subscriber\ActivitySubscriber;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityRepository;
use Hillrange\Form\Type\EntityType;
use Hillrange\Form\Type\TextType;
use Hillrange\Form\Type\ToggleType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
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
     * @var StudentRepository
     */
    private $studentRepository;
    /**
     * ActivityType constructor.
     * @param ActivitySubscriber $activitySubscriber
     */
    public function __construct(ActivitySubscriber $activitySubscriber, StudentRepository $studentRepository)
    {
        $this->activitySubscriber = $activitySubscriber;
        $this->studentRepository = $studentRepository;
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
            ->add('students', EntityType::class,
                [
                    'label' => 'activity.students.label',
                    'multiple' => true,
                    'expanded' => true,
                    'class' => Student::class,
                    'choices' => $this->getStudents($grades, $activities),
                    'help' =>  'activity.students.help',
                    'choice_label' => 'fullName',
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
        $builder->get('tutors')->addEventSubscriber(new SequenceSubscriber());
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

    private function getStudents(array $grades, array $activities)
    {

        $er = $this->studentRepository;

        $xx = $er->createQueryBuilder('s')
            ->leftJoin('s.calendarGrades', 'cg')
            ->where('cg.id IN (:grades)')
            ->setParameter('grades', $grades, Connection::PARAM_STR_ARRAY)
            ->orderBy('s.surname', 'ASC')
            ->addOrderBy('s.firstName', 'ASC')
            ->getQuery()
            ->getResult();

        $yy = $er->createQueryBuilder('s')
            ->leftJoin('s.activities', 'a')
            ->andWhere('a.id IN (:activities)')
            ->setParameter('activities', $activities, Connection::PARAM_STR_ARRAY)
            ->getQuery()
            ->getResult();

        $xx = new ArrayCollection($xx);

        foreach($yy as $student)
            $xx->removeElement($student);

        return $xx->toArray();
    }
}
