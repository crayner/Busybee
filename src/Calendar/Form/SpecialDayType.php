<?php
namespace App\Calendar\Form;

use App\Core\Type\DateType;
use App\Core\Type\HiddenEntityType;
use App\Entity\Calendar;
use App\Entity\SpecialDay;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Doctrine\ORM\EntityManagerInterface;

class SpecialDayType extends AbstractType
{
	/**
	 * @var    EntityManagerInterface
	 */
	private $manager;

	/**
	 * Construct
	 */
	public function __construct(EntityManagerInterface $manager)
	{
		$this->manager = $manager;
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$key  = isset($options['property_path']) ? str_replace(array('[', ']'), '', $options['property_path']) : '__name__';
		$calendar = $options['calendar_data'];
		if (is_null($calendar->getFirstDay()))
			$calendars = array(date('Y'));
		else
			$calendars = range($calendar->getFirstDay()->format('Y'), $calendar->getLastDay()->format('Y'));
		$builder
			->add('day', DateType::class,
				array(
					'label' => 'special_day.day.label',
					'help' => 'special_day.day.help',
					'years' => $calendars,
					'attr' => [
						'class' => 'form-sub-sm'
					],
				)
			)
			->add('type', ChoiceType::class,
				array(
					'label'   => 'special_day.type.label',
					'help'  => 'special_day.type.help',
					'attr'    => array(
						'class' => 'alterType' . $key,
					),
					'choices' => array(
						'special_day.type.closure' => 'closure',
						'special_day.type.alter'   => 'alter',
					),
				)
			)
			->add('name', null,
				array(
					'label' => 'special_day.name.label',
					'help' => 'special_day.name.help',
				)
			)
			->add('description', null,
				array(
					'label'    => 'special_day.description.label',
					'attr'     => array(
						'rows' => '3'
					),
					'help' => 'special_day.description.help',
					'required' => false,
				)
			)
			->add('open', null,
				array(
					'label'       => 'special_day.open.label',
					'attr'        => array(
						'class' => 'alterTime form-sub-sm',
					),
					'help'  => 'special_day.open.help',
					'placeholder' => array('hour' => 'special_day.hour', 'minute' => 'special_day.minute'),
				)
			)
			->add('start', null,
				array(
					'label'       => 'special_day.start.label',
					'attr'        => array(
						'class' => 'alterTime form-sub-sm',
					),
					'help'  => 'special_day.start.help',
					'placeholder' => array('hour' => 'special_day.hour', 'minute' => 'special_day.minute'),
				)
			)
			->add('finish', null,
				array(
					'label'       => 'special_day.finish.label',
					'attr'        => array(
						'class' => 'alterTime form-sub-sm',
					),
					'help'  => 'special_day.finish.help',
					'placeholder' => array('hour' => 'special_day.hour', 'minute' => 'special_day.minute'),
				)
			)
			->add('close', null,
				array(
					'label'       => 'special_day.close.label',
					'attr'        => array(
						'class' => 'alterTime form-sub-sm',
					),
					'help'  => 'special_day.close.help',
					'placeholder' => array('hour' => 'special_day.hour', 'minute' => 'special_day.minute'),
				)
			)
			->add('calendar', HiddenEntityType::class,
				[
					'class' => Calendar::class,
				]
			);
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			array(
				'data_class'         => SpecialDay::class,
				'translation_domain' => 'Calendar',
				'calendar_data'          => null,
			)
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'specialday';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return 'specialday';
	}

	/**
	 * @param FormView      $view
	 * @param FormInterface $form
	 * @param array         $options
	 */
	public function buildView(FormView $view, FormInterface $form, array $options)
	{
		$view->vars['calendar_data'] = $options['calendar_data'];
	}
}
