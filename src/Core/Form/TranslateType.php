<?php
namespace App\Core\Form;

use App\Core\Type\SettingChoiceType;
use App\Entity\Translate;
use Hillrange\Form\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\LocaleType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TranslateType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
            ->add('source', SettingChoiceType::class,
                [
                    'label'                     => 'translate.source.label',
                    'help'                      => 'translate.source.help',
                    'setting_name'              => 'school.search.replace',
                    'choice_translation_domain' => false,
                    'translation_prefix'        => false,
                    'use_value_as_label'        => true,
                ]
            )
            ->add('value', TextType::class,
                [
                    'label'         => 'translate.value.label',
                    'help'          => 'translate.value.help',
                ]
            )
            ->add('locale', LocaleType::class,
                [
                    'label'         => 'translate.locale.label',
                    'help'          => 'translate.locale.help',
                ]
            )

        ;
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			[
				'translation_domain' => 'System',
				'data_class'         => Translate::class,
			]
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'translate';
	}
}
