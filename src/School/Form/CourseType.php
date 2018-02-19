<?php
namespace App\School\Form;

use App\Core\Type\SettingChoiceType;
use App\Entity\Calendar;
use App\Entity\Course;
use App\Entity\Department;
use Doctrine\ORM\EntityRepository;
use Hillrange\CKEditor\Form\CKEditorType;
use Hillrange\Form\Type\EntityType;
use Hillrange\Form\Type\TextType;
use Hillrange\Form\Type\ToggleType;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
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
            ->add('version', TextType::class,
                array(
                    'label' => 'course.version.label',
                    'attr'  => array(
                        'class' => 'monitorChange',
                    ),
                    'required' => false,
                    'help' => 'course.version.help',
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
			->add('targetYears', SettingChoiceType::class,
				[
					'label'                     => 'course.targetYears.label',
                    'help'                      => 'course.targetYears.help',
					'attr'                      => [
						'class' => 'monitorChange small',
					],
					'multiple'                  => true,
					'expanded'                  => true,
					'setting_name'              => 'student.groups',
					'choice_translation_domain' => 'School',
                    'translation_prefix'        => false,
				]
			)
            ->add('calendars', EntityType::class,
                [
                    'class' => Calendar::class,
                    'multiple' => true,
                    'attr' => [
                        'class' => 'monitorChange small',
                    ],
                    'label' => 'course.calendars.label',
                    'help' => 'course.calendars.help',
                    'expanded' => true,
                    'choice_label' => 'name',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                            ->where('c.firstDay >= :dateList')
                            ->setParameter('dateList', date('Y-m-d', strtotime('-5 Years')))
                            ->orderBy('c.firstDay', 'ASC');
                    },
                ]
            )
        ;

		$builder->get('targetYears')->addModelTransformer(new CollectionToArrayTransformer());
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
