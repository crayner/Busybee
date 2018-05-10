<?php
namespace App\School\Form;

use App\Entity\Activity;
use App\Entity\ActivityStudent;
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
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActivityType extends AbstractType
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
            ->add('students', EntityType::class,
                [
                    'label' => 'activity.students.label',
                    'multiple' => true,
                    'expanded' => true,
                    'class' => ActivityStudent::class,
                    'choice_label' => 'fullStudentName',
                    'choice_value' => 'id',
                    'query_builder' => function(EntityRepository $er) use ($grades, $statuses) {
                        return $er->createQueryBuilder('a_s')
                            ->leftJoin('a_s.student', 's')
                            ->orderBy('s.surname', 'ASC')
                            ->addOrderBy('s.firstName', 'ASC')
                            ->groupBy('s.id')
                            ->where('s.id > :zero')
                            ->setParameter('zero', 0)
                            ->leftJoin('a_s.activity', 'a')
                            ->leftJoin('a.calendarGrades', 'cg')
                            ->andWhere('cg.id IN (:grades)')
                            ->setParameter('grades', $grades, Connection::PARAM_INT_ARRAY )
                            ->andWhere('s.status IN (:statuses)')
                            ->setParameter('statuses', $statuses, Connection::PARAM_STR_ARRAY)
                        ;
                    },
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
        ;

		dump($grades);
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
