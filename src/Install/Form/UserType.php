<?php
namespace App\Install\Form;

use App\Core\Manager\SettingManager;
use App\Core\Type\SettingChoiceType;
use App\Core\Validator\SettingChoice;
use App\Install\Validator\GoogleOAuth;
use Hillrange\Form\Type\TextType;
use App\Install\Organism\User;
use Hillrange\Form\Type\ToggleType;
use Hillrange\Security\Validator\Password;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\CurrencyType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Country;
use Symfony\Component\Validator\Constraints\Currency;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    /**
     * @var SettingManager 
     */
    private $settingManager;

    /**
     * UserType constructor.
     */
    public function __construct(SettingManager $settingManager)
    {
        $this->settingManager = $settingManager;
    }

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
						new Password(),
					],
				]
			)
            ->add('surname', TextType::class,
                [
                    'label' => 'person.surname.label',
                    'translation_domain' => 'Person',
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            )
            ->add('firstName', TextType::class,
                [
                    'label' => 'person.firstName.label',
                    'translation_domain' => 'Person',
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            )
            ->add('orgName', TextType::class,
                [
                    'label' => 'system.org_name.label',
                    'data' => $this->settingManager->get('org.name.long'),
                    'translation_domain' => 'System',
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            )
            ->add('orgNameShort', TextType::class,
                [
                    'label' => 'system.org_name_short.label',
                    'translation_domain' => 'System',
                    'data' => $this->settingManager->get('org.name.short'),
                    'constraints' => [
                        new Length(['max' => 5]),
                        new NotBlank(),
                    ],
                ]
            )
            ->add('title', SettingChoiceType::class,
                [
                    'label' => 'person.honorific.label',
                    'translation_domain' => 'Person',
                    'setting_name' => 'person.titlelist',
                    'required' => false,
                    'constraints' => [
                        new SettingChoice(['name' => 'person.titlelist', 'strict' => false,])
                    ],
                ]
            )
            ->add('currency', CurrencyType::class,
                [
                    'label' => 'system.currency.label',
                    'help' => 'system.currency.help',
                    'data' => $this->settingManager->get('currency'),
                    'translation_domain' => 'System',
                    'constraints' => [
                        new NotBlank(),
                        new Currency(),
                    ],
                ]
            )
            ->add('country', CountryType::class,
                [
                    'label' => 'system.country.label',
                    'help' => 'system.country.help',
                    'data' => $this->settingManager->getParameter('country'),
                    'translation_domain' => 'System',
                    'constraints' => [
                        new NotBlank(),
                        new Country(),
                    ],
                ]
            )
            ->add('timezone', TimezoneType::class,
                [
                    'label' => 'system.timezone.label',
                    'help' => 'system.timezone.help',
                    'data' => $this->settingManager->getParameter('timezone'),
                    'translation_domain' => 'System',
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            )
        ;

		GoogleType::buildForm($builder, $options);
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
                'constraints'   => [
                    new GoogleOAuth(),
                ],
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
