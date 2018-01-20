<?php
namespace App\People\Form;

use App\Core\Type\SettingChoiceType;
use App\Entity\CalendarGroup;
use App\Entity\StudentCalendarGroup;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
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
					'label'        => 'calendar.groups.status.label',
					'placeholder'  => 'calendar.groups.status.placeholder',
					'attr'         => [
						'help' => 'calendar.groups.status.help',
					],
				]
			)
			->add('student', HiddenType::class)
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
					'placeholder'   => 'student.calendar.group.placeholder',
					'label'         => 'student.calendar.group.label',
					'attr'          => [
						'help' => 'student.calendar.group.help',
					],
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
