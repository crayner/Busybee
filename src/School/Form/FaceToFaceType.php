<?php
namespace App\School\Form;

use App\Entity\Activity;
use App\Entity\Space;
use App\Entity\Staff;
use App\Entity\Student;
use App\School\Form\Subscriber\ActivitySubscriber;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityRepository;
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
     * ActivityType constructor.
     * @param ActivitySubscriber $activitySubscriber
     */
    public function __construct(ActivitySubscriber $activitySubscriber)
    {
        $this->activitySubscriber = $activitySubscriber;
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
                ]
            )
            ->add('students', EntityType::class,
                [
                    'label' => 'activity.students.label',
                    'multiple' => true,
                    'expanded' => true,
                    'class' => Student::class,
                    'choice_label' => 'fullName',
                    'query_builder' => function(EntityRepository $er) use ($grades) {
                        return $er->createQueryBuilder('s')
                            ->leftJoin('s.calendarGrades', 'cg')
                            ->where('cg.id in (:grades)')
                            ->setParameter('grades', $grades, Connection::PARAM_STR_ARRAY)
                            ->leftJoin('s.courses', 'c')

                            ->orderBy('s.surname', 'ASC')
                            ->addOrderBy('s.firstName', 'ASC')
                        ;
                    },
                    'help' =>  'activity.students.help',
                ]
            )
            ->add('tutors', EntityType::class,
                [
                    'label' => 'activity.tutors.label',
                    'class' => Staff::class,
                    'choice_label' => 'fullName',
                    'multiple' => true,
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
