<?php
namespace App\School\Form;

use Hillrange\Form\Type\ToggleType;
use App\School\Util\Day;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DayType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('sun', ToggleType::class,
				[
					'label' => 'school.day_time.sun.label',
				]
			)
			->add('mon', ToggleType::class,
				[
					'label' => 'school.day_time.mon.label',
				]
			)
			->add('tue', ToggleType::class,
				[
					'label' => 'school.day_time.tue.label',
				]
			)
			->add('wed', ToggleType::class,
				[
					'label' => 'school.day_time.wed.label',
				]
			)
			->add('thu', ToggleType::class,
				[
					'label' => 'school.day_time.thu.label',
				]
			)
			->add('fri', ToggleType::class,
				[
					'label' => 'school.day_time.fri.label',
				]
			)
			->add('sat', ToggleType::class,
				[
					'label' => 'school.day_time.sat.label',
				]
			);
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			[
				'translation_domain' => 'School',
				'data_class'         => Day::class,
			]
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'school_day';
	}
}
