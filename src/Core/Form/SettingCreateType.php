<?php
namespace App\Core\Form;

use App\Core\Validator\Yaml;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class SettingCreateType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('setting', TextareaType::class, array
				(
					'label'  => 'setting.create.setting.label',
					'attr'   => [
						'rows' => 12,
						'cols' => 100,
					],
					'mapped' => false,
					'help' => 'setting.create.setting.help',
                    'constraints' => [
                        new Yaml(),
                    ],
				)
			);
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			array(
				'data_class'         => null,
				'translation_domain' => 'System',
			)
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'create';
	}
}
