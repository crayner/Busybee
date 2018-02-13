<?php
namespace App\People\Form;

use App\Entity\StudentCalendar;
use Hillrange\Form\Type\HiddenEntityType;
use App\Core\Type\SettingChoiceType;
use App\Entity\CalendarGroup;
use App\Entity\Student;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentCalendarType extends AbstractType
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
					'label'        => 'student_calendar.status.label',
					'placeholder'  => 'student_calendar.status.placeholder',
					'help' => 'student_calendar.status.help',
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
					'placeholder'   => 'student_calendar.calendar_group.placeholder',
					'label'         => 'student_calendar.calendar_group.label',
					'help' => 'student_calendar.calendar_group.help',
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
					'data_class'         => StudentCalendar::class,
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
		return 'student_calendar';
	}


}
