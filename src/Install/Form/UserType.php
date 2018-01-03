<?php
namespace App\Install\Form;

use App\Core\Type\TextType;
use App\Core\Validator\Password;
use App\Install\Organism\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('_email', EmailType::class,
				[
					'label'       => 'misc.email.label',
						'help' => 'misc.email.help',
					'constraints' => [
						new NotBlank(),
						new Email(
							[
								'strict'  => true,
								'checkMX' => true,
							]
						),
					],
				]
			)
			->add('_username', TextType::class,
				[
					'label'       => 'misc.username.label',
						'help' => 'misc.username.help',
					'required'    => false,
					'constraints' => [
						new NotBlank(),
					],
				]
			)
			->add('_password', TextType::class,
				[
					'label'       => 'misc.password.label',
						'help' => 'misc.password.help',
					'constraints' => [
						new NotBlank(),
						new Password(['details' => $options['data']]),
					],
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
				'translation_domain' => 'Install',
				'data_class'         => User::class,
			]
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'install_user';
	}


}
