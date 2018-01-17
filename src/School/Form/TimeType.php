<?php
namespace App\School\Form;

use App\School\Util\Time;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class TimeType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('open', \App\Core\Type\TimeType::class,
				[
					'label'       => 'school.day_time.open.label',
					'constraints' => [
						new NotBlank(),
					],
					'help' => 'school.day_time.open.help',
				]
			)
			->add('begin', \App\Core\Type\TimeType::class,
				[
					'label'       => 'school.day_time.begin.label',
					'constraints' => [
						new NotBlank(),
					],
					'help' => 'school.day_time.begin.help',
				]
			)
			->add('finish', \App\Core\Type\TimeType::class,
				[
					'label'       => 'school.day_time.finish.label',
					'constraints' => [
						new NotBlank(),
					],
					'help' => 'school.day_time.finish.help',
				]
			)
			->add('close', \App\Core\Type\TimeType::class,
				[
					'label'       => 'school.day_time.close.label',
					'constraints' => [
						new NotBlank(),
					],
					'help' => 'school.day_time.close.help',
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
				'data_class'         => Time::class,
			]
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'school_time';
	}
}
