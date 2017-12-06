<?php

namespace App\Install\Form;

use App\Core\Type\TextType;
use App\Core\Validator\NoWhiteSpace;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class StartInstallType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('driver', ChoiceType::class,
				[
					'label'       => 'sql.database.driver.label',
					'mapped'      => false,
					'choices'     => [
						'sql.database.driver.PDO MySQL' => 'pdo_mysql',
					],
					'attr'        => [
						'help' => 'sql.database.driver.help',
					],
					'mapped'      => false,
					'constraints' => [
						new NotBlank(),
					],
				]
			)
			->add('port', TextType::class,
				[
					'label'       => 'sql.database.port.label',
					'attr'        => array(
						'help' => 'sql.database.port.help',
					),
					'mapped'      => false,
					'constraints' => [
						new NotBlank(),
					],
				]
			)
			->add('host', TextType::class,
				[
					'label'       => 'sql.database.host.label',
					'attr'        => array(
						'help' => 'sql.database.host.help',
					),
					'mapped'      => false,
					'constraints' => [
						new NotBlank(),
					],
				]
			)
			->add('path', TextType::class,
				[
					'label'       => 'sql.database.name.label',
					'attr'        => array(
						'help' => 'sql.database.name.help',
					),
					'mapped'      => false,
					'constraints' => [
						new NotBlank(),
						new \App\Core\Validator\NoWhiteSpace(
							['repair' => false,]
						),
					],
				]
			)
			->add('user', TextType::class,
				[
					'label'       => 'sql.database.user.label',
					'attr'        => array(
						'help' => 'sql.database.user.help',
					),
					'mapped'      => false,
					'constraints' => [
						new NotBlank(),
						new NoWhiteSpace(
							['repair' => false,]
						),
					],
				]
			)
			->add('password', TextType::class,
				[
					'label'       => 'sql.database.password.label',
					'attr'        => array(
						'help' => 'sql.database.password.help',
					),
					'mapped'      => false,
					'constraints' => [
						new NotBlank(),
					],
				]
			)
			->add('prefix', TextType::class,
				[
					'label'    => 'sql.database.prefix.label',
					'attr'     => array(
						'help' => 'sql.database.prefix.help',
					),
					'mapped'   => false,
					'required' => false,
				]
			);
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'translation_domain' => 'BusybeeInstallBundle',
			'data_class'         => null,
		));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'start_install';
	}
}
