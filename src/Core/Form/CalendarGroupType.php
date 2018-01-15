<?php
namespace App\Core\Form;

use App\Core\Subscriber\CalendarGroupSubscriber;
use App\Core\Type\HiddenEntityType;
use App\Core\Type\SettingChoiceType;
use App\Entity\Calendar;
use App\Entity\CalendarGroup;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CalendarGroupType extends AbstractType
{
	/**
	 * @var CalendarGroupSubscriber
	 */
	private $calendarGroupSubscriber;

	/**
	 * CalendarGroupType constructor.
	 *
	 * @param CalendarGroupSubscriber $calendarGroupSubscriber
	 */
	public function __construct(CalendarGroupSubscriber $calendarGroupSubscriber)
	{
		$this->calendarGroupSubscriber = $calendarGroupSubscriber;
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('nameShort', SettingChoiceType::class,
				[
					'label'        => 'calendar.group.nameshort.label',
					'setting_name' => 'student.groups',
					'required'     => true,
					'placeholder'  => 'calendar.group.nameshort.placeholder',
				]
			)
			->add('name', HiddenType::class)
			->add('calendar', HiddenEntityType::class,
				[
					'class' => Calendar::class,
				]
			)
			->add('sequence', HiddenType::class)
			->add('website', UrlType::class,
				[
					'label'    => 'calendar.group.website.label',
					'required' => false,
				]
			);

		$builder->addEventSubscriber($this->calendarGroupSubscriber);
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			[
				'data_class'         => CalendarGroup::class,
				'translation_domain' => 'Calendar',
				'calendar_data'          => null,
				'error_bubbling'     => true,
			]
		);
		$resolver->setRequired(
			[
				'manager',
			]
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'calendar_group';
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
