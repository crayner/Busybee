<?php

namespace App\Address\Form;

use Hillrange\Form\Type\EntityType;
use App\Core\Type\SettingChoiceType;
use Hillrange\Form\Type\TextType;
use App\Entity\Locality;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class LocalityType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('name', TextType::class, array(
					'label' => 'locality.name.label',
					'help'  => 'locality.name.help',
					'attr'  => array(
						'class' => 'beeLocality monitorChange',
					),
				)
			)
			->add('territory', SettingChoiceType::class, array(
					'label'        => 'locality.territory.label',
					'attr'         => array(
						'class' => 'beeTerritory monitorChange',
					),
					'setting_name' => 'address.territory.list',
					'placeholder'  => 'locality.territory.placeholder',
					'translation_prefix' => false,
				)
			)
			->add('postCode', TextType::class, array(
					'label' => 'locality.postcode.label',
					'attr'  => array(
						'class' => 'beePostCode monitorChange',
					),
				)
			)
			->add('country', CountryType::class, array(
					'label' => 'locality.country.label',
					'attr'  => array(
						'class' => 'beeCountry monitorChange',
					),
				)
			)
			->add('localityList', EntityType::class,
				array(
					'class'         => Locality::class,
					'label'         => 'locality.localityList.label',
					'choice_label'  => 'fullLocality',
					'placeholder'   => 'locality.localityList.placeholder',
					'help'  => 'locality.localityList.help',
					'required'      => false,
					'attr'          => array(
						'class' => 'beeLocalityList formChanged',
						'autocomplete' => 'off',
					),
					'mapped'        => false,
					'query_builder' => function (EntityRepository $lr) {
						return $lr->createQueryBuilder('l')
							->orderBy('l.name', 'ASC')
							->addOrderBy('l.postCode', 'ASC');
					},
				)
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
				'data_class'         => Locality::class,
				'translation_domain' => 'Person',
				'allow_extra_fields' => true,
				'classSuffix'        => null,
			]
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'locality';
	}
}
