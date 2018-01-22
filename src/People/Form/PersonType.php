<?php
namespace App\People\Form;

use App\Address\Form\PhoneType;
use App\Core\Manager\SettingManager;
use App\Core\Type\AutoCompleteType;
use App\Core\Type\ImageType;
use App\Core\Type\SettingChoiceType;
use App\Core\Validator\SettingChoice;
use App\Entity\Address;
use App\Entity\Person;
use App\People\Form\Subscriber\PersonSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
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
					'setting_name' => 'person.titlelist',
					'attr'         => array(
						'class' => 'beeHonorific',
					),
					'required'     => false,
					'constraints'   => [
						new SettingChoice(['name' => 'person.titlelist']),
					],

				)
			)
			->add('identifier', HiddenType::class,
				[
				]
			)
			->add('surname', null, array(
					'label' => 'person.surname.label',
					'attr'  => array(
						'class' => 'beeSurname',
					),
				)
			)
			->add('firstName', null, array(
					'label' => 'person.firstName.label',
					'attr'  => array(
						'class' => 'beeFirstName',
					),
				)
			)
			->add('preferredName', null, array(
					'label'    => 'person.preferredName.label',
					'attr'     => array(
						'class' => 'beePreferredName',
					),
					'required' => false,
				)
			)
			->add('officialName', null, array(
					'label' => 'person.officialName.label',
					'help'  => 'person.officialName.help',
					'attr'  => array(
						'class' => 'beeOfficialName',
					),
				)
			)
			->add('gender', SettingChoiceType::class, array(
					'setting_name'              => 'person.genderlist',
					'label'                     => 'person.gender.label',
					'attr'                      => array(
						'class' => 'beeGender',
					),
					'constraints'   => [
						new SettingChoice(['name' => 'person.genderlist']),
					],
				)
			)
			->add('dob', BirthdayType::class, array(
					'label'    => 'person.dob.label',
					'required' => false,
					'attr'     => array(
						'class' => 'beeDob',
					),
					'format' => $this->settingManager->get('date.format.widget'),

	)
			)
			->add('email', EmailType::class, array(
					'label'    => 'person.email.label',
					'required' => false,
						'help' => 'person.email.help',
				)
			)
			->add('email2', EmailType::class, array(
					'label'    => 'person.email2.label',
					'required' => false,
				)
			)
			->add('photo', ImageType::class, array(
					'attr'        => array(
						'imageClass' => 'headShot75',
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
						'autocomplete' => 'new-address',
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
						'autocomplete' => 'new-address',
					),
				)
			)
			->add('phone', CollectionType::class, array(
					'label'              => 'person.phones.label',
					'entry_type'         => PhoneType::class,
					'allow_add'          => true,
					'by_reference'       => false,
					'allow_delete'       => true,
					'attr'               => array(
						'class' => 'phoneNumberList'
					),
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
