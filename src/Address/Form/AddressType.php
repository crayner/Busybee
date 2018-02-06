<?php
namespace App\Address\Form;

use App\Address\Form\Subscriber\AddressSubscriber;
use App\Core\Type\AutoCompleteType;
use Hillrange\Form\Type\EntityType;
use App\Core\Type\SettingChoiceType;
use App\Entity\Address;
use App\Entity\Locality;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
	/**
	 * @var AddressSubscriber
	 */
	private $addressSubscriber;

	/**
	 * Construct
	 */
	public function __construct(AddressSubscriber $addressSubscriber)
	{
		$this->addressSubscriber = $addressSubscriber;
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('buildingType', SettingChoiceType::class,
				array(
					'label'        => 'address.buildingType.label',
					'attr'         => array(
						'class' => 'beeBuildingType monitorChange',
					),
					'help'  => 'address.buildingType.help',
					'setting_name' => 'address.buildingtype',
					'required'     => false,
				)
			)
			->add('buildingNumber', null, array(
					'label'    => 'address.buildingNumber.label',
					'attr'     => array(
						'maxLength' => 10,
						'class'     => 'beeBuildingNumber monitorChange',
					),
					'help'      => 'address.buildingNumber.help',
					'required' => false,
				)
			)
			->add('streetNumber', null, array(
					'label'    => 'address.streetNumber.label',
					'attr'     => array(
						'maxLength' => 10,
						'class'     => 'beeStreetNumber monitorChange',
					),
					'help'      => 'address.streetNumber.help',
					'required' => false,
				)
			)
			->add('propertyName', null, array(
					'label'    => 'address.propertyName.label',
					'attr'     => array(
						'class' => 'beePropertyName monitorChange',
					),
					'help'  => 'address.propertyName.help',
					'required' => false,
				)
			)
			->add('streetName', null, array(
					'label' => 'address.streetName.label',
					'attr'  => array(
						'class' => 'beeStreetName monitorChange',
					),
					'help'  => 'address.streetName.help',
				)
			)
			->add('locality', EntityType::class,
				array(
					'class'         => Locality::class,
					'label'         => 'address.locality.label',
					'choice_label'  => 'fullLocality',
					'help'  => 'address.locality.help',
					'placeholder'   => 'address.locality.placeholder',
					'attr'          => array(
						'class' => 'beeLocality monitorChange',
						'autocomplete' => 'off',
					),
					'query_builder' => function (EntityRepository $lr) {
						return $lr->createQueryBuilder('l')
							->orderBy('l.name', 'ASC')
							->addOrderBy('l.postCode', 'ASC');
					},
				)
			)
			->add('addressList', AutoCompleteType::class,
				array(
					'class'         => Address::class,
					'label'         => 'address.addressList.label',
					'choice_label'  => 'singleLineAddress',
					'empty_data'    => null,
					'help'          => 'address.addressList.help',
					'required'      => false,
					'attr'          => array(
						'class'     => 'beeAddressList formChanged',
						'autocomplete' => 'new-address',
					),
					'mapped'        => false,
				)
			);

		$builder->addEventSubscriber($this->addressSubscriber);

	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
				'data_class'         => Address::class,
				'translation_domain' => 'Person',
				'classSuffix'        => null,
				'allow_extra_fields' => true,
			)
		);
		$resolver->setRequired(
			[
				'manager',
			]
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'address';
	}


}
