<?php
namespace App\Calendar\Form;

use App\Entity\CalendarGrade;
use Doctrine\ORM\EntityRepository;
use Hillrange\Form\Type\EntityType;
use Hillrange\Form\Type\HiddenEntityType;
use App\Core\Type\SettingChoiceType;
use App\Entity\Calendar;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CalendarGradeType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
	    $nextCalendar = $options['manager']->getNextCalendar($options['calendar_data']);
	    $builder
			->add('grade', SettingChoiceType::class,
				[
                    'label'         => 'calendar_grade.grade.label',
                    'placeholder'   => 'calendar_grade.grade.placeholder',
					'required'      => true,
                    'setting_name'  => 'student.groups',
                    'help'          => 'calendar_grade.grade.help',
				]
			)
			->add('nextGrade', EntityType::class,
                [
                    'label'         => 'calendar_grade.next_grade.label',
                    'placeholder'   => 'calendar_grade.next_grade.placeholder',
                    'required'      => false,
                    'class'         => CalendarGrade::class,
                    'choice_label'  => 'fullName',
                    'help'          => 'calendar_grade.next_grade.help',
                    'query_builder' => function (EntityRepository $er) use ($nextCalendar) {
                        return $er->createQueryBuilder('g')
                            ->leftJoin('g.calendar', 'c')
                            ->where('c.id = :calendar_id')
                            ->setParameter('calendar_id', $nextCalendar ? $nextCalendar->getId() : 0)
                            ->orderBy('g.sequence', 'ASC')
                        ;
                    },
                ]
            )
			->add('calendar', HiddenEntityType::class,
				[
					'class'         => Calendar::class,
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
			[
				'data_class'         => CalendarGrade::class,
				'translation_domain' => 'Calendar',
				'error_bubbling'     => true,
			]
		);
		$resolver->setRequired(
			[
				'manager',
                'calendar_data',
			]
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'calendar_grade';
	}

	/**
	 * @param FormView      $view
	 * @param FormInterface $form
	 * @param array         $options
	 */
	public function buildView(FormView $view, FormInterface $form, array $options)
	{
		$view->vars['calendar_data'] = $options['calendar_data'];
		$view->vars['manager']   = $options['manager'];
	}
}
