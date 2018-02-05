<?php
namespace App\School\Form;

use Hillrange\Form\Type\HiddenEntityType;
use App\Core\Type\SettingChoiceType;
use App\Entity\Department;
use App\Entity\DepartmentMember;
use App\Entity\Staff;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DepartmentMemberType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$options['staff_type'] = $options['staff_type'] == 'Learning Area' ? 'Learning' : 'Administration';
dump($options);
		$builder
			->add('staff', EntityType::class,
				[
					'label'         => 'department.members.member.label',
					'class'         => Staff::class,
					'choice_label'  => 'formatName',
					'query_builder' => function (EntityRepository $er) {
						return $er->createQueryBuilder('s')
							->orderBy('s.surname', 'ASC')
							->addOrderBy('s.firstName', 'ASC');
					},
					'placeholder'   => 'department.members.member.placeholder',
					'help' => 'department.members.member.help',
				]
			)
			->add('staffType', SettingChoiceType::class,
				[
					'label'        => 'department.members.type.label',
					'setting_name' => 'department.staff.type.list.' . strtolower($options['staff_type']),
					'placeholder'  => 'department.members.type.placeholder',
				]
			)
			->add('department', HiddenEntityType::class,
				[
					'class' => Department::class,
				]
			);

	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class'         => DepartmentMember::class,
			'translation_domain' => 'School',
		));
		$resolver->setRequired(
			[
				'staff_type',
			]
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'department_member';
	}
}
