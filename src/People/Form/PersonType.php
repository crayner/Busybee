<?php
namespace App\People\Form;

use App\Address\Form\PhoneType;
use App\Core\Manager\SettingManager;
use App\Core\Type\AutoCompleteType;
use Hillrange\Form\Type\CollectionType;
use Hillrange\Form\Type\ImageType;
use App\Core\Type\SettingChoiceType;
use App\Entity\Address;
use App\Entity\Person;
use App\People\Form\Subscriber\PersonSubscriber;
use Hillrange\Form\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonType extends AbstractType
{
	/**
	 * @var PersonSubscriber
	 */
	private $personSubscriber;

	/**
	 * @var SettingManager
	 */
	private $settingManager;

	/**
	 * Construct
	 */
	public function __construct(PersonSubscriber $personSubscriber, SettingManager $settingManager)
	{
		$this->personSubscriber = $personSubscriber;
		$this->settingManager = $settingManager;
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('honorific', SettingChoiceType::class, array(
					'label'        => 'person.honorific.label',
					'setting_name' => 'person.title.list',
					'attr'         => array(
						'class' => 'beeHonorific',
					),
					'required'     => false,
                    'use_lower_case' => true,
                    'strict_validation' => false,
				)
			)
			->add('identifier', HiddenType::class,
				[
				]
			)
			->add('surname', TextType::class, array(
					'label' => 'person.surname.label',
					'attr'  => array(
						'class' => 'beeSurname',
					),
                    'auto_complete' => 'family-name',
				)
			)
			->add('firstName', TextType::class, array(
					'label' => 'person.firstName.label',
					'attr'  => array(
						'class' => 'beeFirstName',
					),
                    'auto_complete' => 'given-name',
				)
			)
			->add('preferredName', TextType::class, array(
					'label'    => 'person.preferredName.label',
					'attr'     => array(
						'class' => 'beePreferredName',
					),
					'required' => false,
				)
			)
			->add('officialName', TextType::class, array(
					'label' => 'person.officialName.label',
					'help'  => 'person.officialName.help',
					'attr'  => array(
						'class' => 'beeOfficialName',
					),
				)
			)
			->add('gender', SettingChoiceType::class, [
					'setting_name'              => 'person.gender.list',
					'label'                     => 'person.gender.label',
					'attr'                      => [
						'class'                     => 'beeGender',
					],
                    'use_lower_case'            => true,
                    'strict_validation'         => true,
				]
			)
			->add('dob', BirthdayType::class, array(
					'label'    => 'person.dob.label',
					'required' => false,
					'attr'     => array(
						'class' => 'beeDob',
					),
					'format' => $this->settingManager->get('date.format.widget', 'dMMMy'),

	)
			)
			->add('email', EmailType::class, array(
					'label'    => 'person.email.label',
					'required' => false,
						'help' => 'person.email.help',
                    'auto_complete' => 'email',
				)
			)
			->add('email2', EmailType::class, array(
					'label'    => 'person.email2.label',
					'required' => false,
                    'auto_complete' => 'email',
				)
			)
			->add('photo', ImageType::class, array(
					'attr'        => array(
						'imageClass' => 'headShot75 img-thumbnail',
					),
					'help'       => 'person.photo.help',
					'label'       => 'person.photo.label',
					'required'    => false,
					'deletePhoto' => $options['deletePhoto'],
					'fileName'    => 'person_' . $options['data']->getId(),
				)
			)
			->add('website', UrlType::class, array(
					'label'    => 'person.website.label',
					'required' => false,
				)
			)
			->add('address1', AutoCompleteType::class,
				array(
					'class'        => Address::class,
//					'data'         => $options['data']->getAddress1(),
					'choice_label' => 'singleLineAddress',
					'empty_data'   => null,
					'required'     => false,
					'label'        => 'person.address1.label',
					'help'  => 'person.address1.help',
					'attr'         => array(
						'class' => 'beeAddressList formChanged',
					),
				)
			)
			->add('address2', AutoCompleteType::class,
				array(
					'class'        => Address::class,
					'choice_label' => 'singleLineAddress',
//					'data'         => $options['data']->getAddress2(),
					'empty_data'   => null,
					'required'     => false,
					'help'  => 'person.address2.help',
					'label'        => 'person.address2.label',
					'attr'         => array(
						'class' => 'beeAddressList formChanged',
					),
				)
			)
			->add('phones', CollectionType::class, array(
					'label'              => 'person.phones.label',
					'entry_type'         => PhoneType::class,
					'allow_add'          => true,
					'by_reference'       => false,
					'allow_delete'       => true,
					'translation_domain' => 'Person',
					'required'           => false,
				)
			);
		$builder->addEventSubscriber($this->personSubscriber);
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			array(
				'data_class'         => Person::class,
				'translation_domain' => 'Person',
				'allow_extra_fields' => true,
                'attr'               => [
                    'noValidate' => '',
                ],
			)
		);
		$resolver->setRequired(
			[
				'deletePhoto',
				'isSystemAdmin',
				'deletePassportScan',
				'deleteIDScan',
				'session',
			]
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'person';
	}
}
