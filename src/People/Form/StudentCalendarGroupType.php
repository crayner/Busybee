<?php
namespace App\People\Form;

use App\Core\Type\HiddenEntityType;
use App\Core\Type\SettingChoiceType;
use App\Entity\CalendarGroup;
use App\Entity\Student;
use App\Entity\StudentCalendarGroup;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentCalendarGroupType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('status', SettingChoiceType::class,
				[
					'setting_name' => 'student.enrolment.status',
					'label'        => 'calendar_group.status.label',
					'placeholder'  => 'calendar_group.status.placeholder',
					'help' => 'calendar_group.status.help',
				]
			)
			->add('student', HiddenEntityType::class,
				[
					'class' => Student::class,
				]
			)
			->add('calendarGroup', EntityType::class,
				[
					'class'         => CalendarGroup::class,
					'choice_label'  => 'fullName',
					'query_builder' => function (EntityRepository $er) {
						return $er->createQueryBuilder('g')
							->leftJoin('g.calendar', 'c')
							->orderBy('c.firstDay', 'DESC')
							->addOrderBy('g.sequence', 'ASC');
					},
					'placeholder'   => 'student.calendar_group.placeholder',
					'label'         => 'student.calendar_group.label',
					'help' => 'student.calendar_group.help',
				]
			);
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver
			->setDefaults(
				[
					'data_class'         => StudentCalendarGroup::class,
					'translation_domain' => 'Student',
					'error_bubbling'     => true,
				]
			);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'calendar_group_by_student';
	}


}
