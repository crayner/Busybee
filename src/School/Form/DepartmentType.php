<?php
namespace App\School\Form;

use App\Core\Manager\SettingManager;
use App\Entity\Course;
use Hillrange\CKEditor\Form\CKEditorType;
use Hillrange\Form\Type\CollectionEntityType;
use Hillrange\Form\Type\CollectionType;
use Hillrange\Form\Type\EntityType;
use Hillrange\Form\Type\ImageType;
use App\Core\Type\SettingChoiceType;
use App\Entity\Department;
use App\School\Form\Subscriber\DepartmentSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DepartmentType extends AbstractType
{

	/**
	 * @var EntityManagerInterface
	 */
	private $ds;

	/**
	 * DepartmentType constructor.
	 *
	 * @param SettingManager $om
	 */
	public function __construct(DepartmentSubscriber $ds)
	{
		$this->ds = $ds;
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
        $dept_id = $options['data'] instanceof Department ? $options['data']->getId() : 0;
		$builder
			->add('name', null,
				[
					'label' => 'department.name.label'
				]
			)
			->add('type', SettingChoiceType::class,
				[
					'label'        => 'department.type.label',
					'setting_name' => 'department.type.list',
					'placeholder'  => 'department.type.placeholder',
				]
			)
			->add('nameShort', null,
				[
					'label' => 'department.nameShort.label'
				]
			)
			->add('departmentList', EntityType::class, array(
					'class'         => Department::class,
					'attr'          => array(
						'class' => 'departmentList changeRecord formChanged form-control-sm',
					),
					'label'         => '',
					'mapped'        => false,
					'choice_label'  => 'name',
					'query_builder' => function (EntityRepository $er) {
						return $er->createQueryBuilder('d')
							->orderBy('d.name', 'ASC');
					},
					'placeholder'   => 'department.departments.placeholder',
					'required'      => false,
					'data'          => $options['data']->getId(),
				)
			)
			->add('logo', ImageType::class,
				[
					'label'       => 'department.logo.label',
					'required'    => false,
					'attr'        => [
						'imageClass' => 'smallLogo'
					],
					'deletePhoto' => $options['deletePhoto'],
					'fileName'    => 'departmentLogo'
				]
			)
			->add('blurb', CKEditorType::class,
				[
					'label'    => 'department.blurb.label',
					'attr'     => [
						'rows' => 4,
					],
					'required' => false,
				]
			)
			->add('courses', CollectionType::class,
                [
                    'help' => 'department.course.help',
                    'label' => false,
                    'entry_type' => CollectionEntityType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'entry_options' => [
                        'query_builder' => function (EntityRepository $er) use ($dept_id) {
                            return $er->createQueryBuilder('c')
                                ->leftJoin('c.department', 'd')
                                ->where('(d.id IS NULL OR d.id = :dept_id)')
                                ->setParameter('dept_id', $dept_id)
                                ->orderBy('c.name')
                            ;
                        },
                        'class' => Course::class,
                        'block_prefix' => 'department_course',
                        'choice_label' => 'fullName',
                        'placeholder' => 'department.course.name.placeholder',
                    ],
                    'route' => 'department_courses_manage',
                ]
            )
        ;

		$builder->addEventSubscriber($this->ds);
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			[
				'data_class'         => Department::class,
				'translation_domain' => 'School',
				'deletePhoto'        => null,
                'attr'               => [
                    'novalidate' => '',
                ],
			]
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'department';
	}


}
