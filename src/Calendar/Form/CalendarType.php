<?php
namespace App\Calendar\Form;

use App\Calendar\Validator\CalendarDate;
use App\Calendar\Validator\CalendarStatus;
use App\Calendar\Validator\RollGroup;
use App\Calendar\Validator\SpecialDayDate;
use App\Calendar\Validator\TermDate;
use App\Core\Manager\SettingManager;
use App\Calendar\Listener\CalendarSubscriber;
use App\Entity\CalendarGrade;
use Hillrange\Form\Type\DateType;
use App\Entity\Calendar;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class CalendarType extends AbstractType
{
	/**
	 * @var array
	 */
	private $statusList;

	/**
	 * @var CalendarSubscriber
	 */
	private $calendarSubscriber;

	/**
	 * CalendarType constructor.
	 *
	 * @param SettingManager     $settingManager
	 * @param CalendarSubscriber $calendarSubscriber
	 */
	public function __construct(SettingManager $settingManager, CalendarSubscriber $calendarSubscriber)
	{
		$this->statusList = $settingManager->get('calendar.status.list');
		$this->calendarSubscriber = $calendarSubscriber;
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{

		$builder
			->add('name', null,
				array(
					'label' => 'calendar.name.label',
					'attr'  => array(
						'help' => 'calendar.name.help',
					),
				)
			)
			->add('firstDay', DateType::class,
				array(
					'label' => 'calendar.firstDay.label',
					'help' => 'calendar.firstDay.help',
				)
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
			->add('status', ChoiceType::class,
				array(
					'label'       => 'calendar.status.label',
					'help' => 'calendar.status.help',
					'choices'     => $this->statusList,
					'placeholder' => 'calendar.status.placeholder',
					'constraints' => array(
						new CalendarStatus(array('id' => is_null($options['data']->getId()) ? 'Add' : $options['data']->getId())),
					),
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
					'attr'          => array(
						'class' => 'termList'
					),
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
					'attr'          => array(
						'class' => 'specialDayList'
					),
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
                    'attr'          => array(
                        'class' => 'calendarGradeList'
                    ),
                    'by_reference'  => false,
                ]
            )
            ->add('rollGroups', CollectionType::class, [
                    'entry_type'    => RollGroupType::class,
                    'allow_add'     => true,
                    'entry_options' => array(
                        'calendar_data' => $options['data'],
                        'manager'   => $options['rollGroupManager'],
                    ),
                    'constraints'   => [
                        new RollGroup(['calendar' => $options['data']]),
                    ],
                    'label'         => false,
                    'allow_delete'  => true,
                    'attr'          => array(
                        'class' => 'rollGroupList'
                    ),
                    'by_reference'  => false,
                ]
            )
			->add('downloadCache', HiddenType::class);

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
			)
		);
		$resolver->setRequired(
			[
				'rollGroupManager',
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
