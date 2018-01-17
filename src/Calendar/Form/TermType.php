<?php
namespace App\Calendar\Form;

use App\Core\Type\DateType;
use App\Core\Type\HiddenEntityType;
use App\Core\Type\TextType;
use App\Entity\Calendar;
use App\Entity\Term;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TermType extends AbstractType
{
	/**
	 * @var    EntityManagerInterface
	 */
	private $entityManager;

	/**
	 * Construct
	 */
	public function __construct(EntityManagerInterface $manager)
	{
		$this->entityManager = $manager;
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$calendar  = $options['calendar_data'];
		$calendars = array();
		if (!is_null($calendar->getFirstDay()))
		{
			$calendars[] = $calendar->getFirstDay()->format('Y');
			if ($calendar->getFirstDay()->format('Y') !== $calendar->getLastDay()->format('Y'))
				$calendars[] = $calendar->getLastDay()->format('Y');
		}
		else
			$calendars[] = date('Y');
		$builder
			->add('name', TextType::class,
				array(
					'label' => 'term.name.label',
					'help' => 'term.name.help',
					'attr'  => array(
					),
				)
			)
			->add('nameShort', TextType::class,
				array(
					'label' => 'term.name_short.label',
					'help' => 'term.name_short.help',
				)
			)
			->add('firstDay', DateType::class,
				array(
					'label' => 'calendar.firstDay.label',
					'help' => 'calendar.firstDay.help',
					'years' => $calendars,
				)
			)
			->add('lastDay', DateType::class,
				array(
					'label' => 'calendar.lastDay.label',
					'help' => 'calendar.lastDay.help',
					'years' => $calendars,
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
				'data_class'         => Term::class,
				'translation_domain' => 'Calendar',
				'calendar_data'      => null,
				'error_bubbling'     => true,
			)
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'term';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return 'term';
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
