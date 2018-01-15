<?php
namespace App\Core\Form;

use App\Core\Manager\SettingManager;
use App\Core\Subscriber\CalendarSubscriber;
use App\Core\Type\DateType;
use App\Core\Validator\CalendarDate;
use App\Core\Validator\CalendarGroup;
use App\Core\Validator\CalendarStatus;
use App\Core\Validator\SpecialDayDate;
use App\Core\Validator\TermDate;
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
			->add('calendarGroups', CollectionType::class, array(
					'entry_type'    => CalendarGroupType::class,
					'allow_add'     => true,
					'entry_options' => array(
						'calendar_data' => $options['data'],
						'manager'   => $options['calendarGroupManager'],
					),
					'constraints'   => [
						new CalendarGroup(['calendar' => $options['data']]),
					],
					'label'         => false,
					'allow_delete'  => true,
					'attr'          => array(
						'class' => 'calendarGroupList'
					),
					'by_reference'  => false,
				)
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
				'calendarGroupManager',
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
