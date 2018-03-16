<?php
namespace App\Calendar\Form;

use App\Calendar\Validator\CalendarDate;
use App\Calendar\Validator\CalendarStatus;
use App\Calendar\Validator\SpecialDayDate;
use App\Calendar\Validator\TermDate;
use App\Calendar\Form\Listener\CalendarSubscriber;
use App\Core\Type\SettingChoiceType;
use App\Entity\Student;
use Doctrine\ORM\EntityRepository;
use Hillrange\Form\Type\CollectionEntityType;
use Hillrange\Form\Type\CollectionType;
use Hillrange\Form\Type\DateType;
use App\Entity\Calendar;
use Hillrange\Form\Type\EntityType;
use Hillrange\Form\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CalendarType extends AbstractType
{
	/**
	 * @var CalendarSubscriber
	 */
	private $calendarSubscriber;

	/**
	 * CalendarType constructor.
	 *
	 * @param CalendarSubscriber $calendarSubscriber
	 */
	public function __construct(CalendarSubscriber $calendarSubscriber)
	{
		$this->calendarSubscriber = $calendarSubscriber;
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('name', TextType::class,
				[
					'label' => 'calendar.name.label',
					'attr'  => [
						'help' => 'calendar.name.help',
					],
				]
			)
			->add('firstDay', DateType::class,
				[
					'label' => 'calendar.firstDay.label',
					'help' => 'calendar.firstDay.help',
				]
			)
			->add('lastDay', DateType::class,
				array(
					'label'       => 'calendar.lastDay.label',
					'help' => 'calendar.lastDay.help',
					'constraints' => array(
						new CalendarDate(['fields' => $options['data']]),
					),
				)
			)
			->add('status', SettingChoiceType::class,
				array(
					'label'         => 'calendar.status.label',
					'help'          => 'calendar.status.help',
					'setting_name'  => 'calendar.status.list',
					'placeholder'   => 'calendar.status.placeholder',
					'constraints'   => [
						new CalendarStatus(array('id' => is_null($options['data']->getId()) ? 'Add' : $options['data']->getId())),
					],
                    'translation_prefix' => false,
                    'choice_translation_domain' => 'Calendar',
				)
			)
			->add('terms', CollectionType::class, array(
					'entry_type'    => TermType::class,
					'allow_add'     => true,
					'entry_options' => array(
						'calendar_data' => $options['data'],
					),
					'constraints'   => array(
						new TermDate(['calendar' => $options['data']]),
					),
					'label'         => false,
					'by_reference'  => false,
				)
			)
			->add('specialDays', CollectionType::class, array(
					'entry_type'    => SpecialDayType::class,
					'allow_add'     => true,
					'entry_options' => array(
						'calendar_data' => $options['data'],
					),
					'constraints'   => array(
						new SpecialDayDate(['calendar' => $options['data']]),
					),
					'label'         => false,
					'allow_delete'  => true,
					'by_reference'  => false,
				)
			)
            ->add('calendarGrades', CollectionType::class, [
                    'entry_type'    => CalendarGradeType::class,
                    'allow_add'     => true,
                    'entry_options' => [
                        'calendar_data' => $options['data'],
                        'manager'   => $options['calendarGradeManager'],
                    ],
                    'label'         => false,
                    'allow_delete'  => true,
                    'allow_up'      => true,
                    'allow_down'    => true,
                    'by_reference'  => false,
                    'sort_manage'   => true,
                ]
            )
			->add('downloadCache', HiddenType::class)
        ;

		$builder->addEventSubscriber($this->calendarSubscriber);
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			array(
				'data_class'         => Calendar::class,
				'translation_domain' => 'Calendar',
                'calendar_data' => null,
			)
		);
		$resolver->setRequired(
			[
                'calendarGradeManager',
			]
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'calendar';
	}


}
