<?php
namespace App\Install\Form;

use Hillrange\Form\Type\TextType;
use Hillrange\Form\Type\ToggleType;
use App\Install\Organism\Miscellaneous;
use App\Install\Subscriber\MiscellaneousSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\LocaleType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class MiscellaneousType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('secret', HiddenType::class)
			->add('sessionName', TextType::class,
				[
					'label'       => 'misc.session_name.label',
						'help' => 'misc.session_name.help',
					'constraints' => [
						new NotBlank(),
					],
				]
			)
			->add('sessionMaxIdleTime', TextType::class,
				[
					'label'       => 'misc.session_max_idle_time.label',
						'help' => 'misc.session_max_idle_time.help',
					'constraints' => [
						new NotBlank(),
					],
				]
			)
			->add('signInCountMinimum', TextType::class,
				[
					'label'       => 'misc.sign_in_count_minimum.label',
						'help' => 'misc.sign_in_count_minimum.help',
					'constraints' => [
						new NotBlank(),
					],
				]
			)
			->add('sessionRememberMeName', TextType::class,
				[
					'label'       => 'misc.session_remember_me_name.label',
					'help' => 'misc.session_remember_me_name.help',
					'constraints' => [
						new NotBlank(),
					],
				]
			)
			->add('country', CountryType::class,
				[
					'label'       => 'misc.country.label',
						'help' => 'misc.country.help',
					'constraints' => [
						new NotBlank(),
					],
				]
			)
			->add('timezone', TimezoneType::class,
				[
					'label'       => 'misc.timezone.label',
						'help' => 'misc.timezone.help',
					'constraints' => [
						new NotBlank(),
					],
				]
			)
			->add('locale', LocaleType::class,
				[
					'label'       => 'misc.locale.label',
						'help' => 'misc.locale.help',
					'constraints' => [
						new NotBlank(),
					],
				]
			)
			->add('passwordMinLength', ChoiceType::class,
				[
					'label'   => 'misc.password.minLength.label',
						'help' => 'misc.password.minLength.help',
					'choices' => [
						8  => 8,
						9  => 9,
						10 => 10,
						11 => 11,
						12 => 12,
						13 => 13,
						14 => 14,
						15 => 15,
						16 => 16,
					],
				]
			)
			->add('hemisphere', ChoiceType::class,
				[
					'label'   => 'misc.hemisphere.label',
						'help' => 'misc.hemisphere.help',
					'choices' => [
						'misc.hemisphere.choice.north' => 'North',
						'misc.hemisphere.choice.south' => 'South',
					],
				]
			)
			->add('passwordNumbers', ToggleType::class,
				[
					'label'  => 'misc.password.numbers.label',
						'help' => 'misc.password.numbers.help',
				]
			)
			->add('passwordMixedCase', ToggleType::class,
				[
					'label'  => 'misc.password.mixedCase.label',
						'help' => 'misc.password.mixedCase.help',
				]
			)
			->add('passwordSpecials', ToggleType::class,
				[
					'label'  => 'misc.password.specials.label',
						'help' => 'misc.password.specials.help',
				]
			)
        ;
		$builder->addEventSubscriber(new MiscellaneousSubscriber());
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'translation_domain' => 'Install',
			'data_class'         => Miscellaneous::class,
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'install_miscellaneous';
	}


}
