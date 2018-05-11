<?php
namespace App\School\Form;

use App\Calendar\Util\CalendarManager;
use App\Entity\CalendarGrade;
use App\Entity\Course;
use App\Entity\Department;
use Doctrine\ORM\EntityRepository;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Hillrange\Form\Type\EntityType;
use Hillrange\Form\Type\TextType;
use Hillrange\Form\Type\ToggleType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CourseType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
	    $cal = CalendarManager::getCurrentCalendar();
		$builder
			->add('name', TextType::class,
				array(
					'label' => 'course.name.label',
					'attr'  => array(
						'class' => 'monitorChange',
					),
				)
			)
			->add('code', TextType::class,
				array(
					'label' => 'course.code.label',
					'attr'  => [
						'class' => 'monitorChange',
					],
                    'help'  => 'course.code.help',
				)
			)
            ->add('description', CKEditorType::class,
                array(
                    'label' => 'course.description.label',
                    'attr'  => array(
                        'class' => 'monitorChange',
                    ),
                )
            )
            ->add('map', ToggleType::class,
                array(
                    'label' => 'course.map.label',
                    'attr'  => array(
                        'class' => 'monitorChange',
                    ),
                )
            )
            ->add('department', EntityType::class,
                [
                    'label' => 'course.department.label',
                    'class' => Department::class,
                    'choice_label' => 'name',
                    'placeholder' => 'course.department.placeholder',
                ]
            )
			->add('calendarGrades', EntityType::class,
				[
				    'class'                     => CalendarGrade::class,
					'label'                     => 'course.calendar_grades.label',
                    'help'                      => 'course.calendar_grades.help',
                    'placeholder'                      => 'course.calendar_grades.placeholder',
					'attr'                      => [
						'class' => 'monitorChange small',
					],
					'multiple'                  => true,
					'expanded'                  => true,
                    'choice_label'              => 'fullName',
                    'query_builder'             => function (EntityRepository $er) use ($cal)
                    {
                        return $er->createQueryBuilder('g')
                            ->leftJoin('g.calendar', 'c')
                            ->where('c.id = :cal')
                            ->setParameter('cal', $cal)
                            ->orderBy('g.sequence', 'ASC')
                        ;
                    },
				]
			)
        ;
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			array(
				'data_class'         => Course::class,
				'translation_domain' => 'School',
			)
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'course';
	}
}
