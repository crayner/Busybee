<?php
namespace App\Address\Form;

use App\Address\Form\Transformer\PhoneTransformer;
use App\Address\Form\Subscriber\PhoneSubscriber;
use App\Core\Type\SettingChoiceType;
use App\Core\Type\TextType;
use App\Entity\Phone;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PhoneType extends AbstractType
{
	/**
	 * @var PhoneSubscriber
	 */
	private $phoneSubscriber;

	/**
	 * Construct
	 */
	public function __construct(PhoneSubscriber $phoneSubscriber)
	{
		$this->phoneSubscriber = $phoneSubscriber;
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('phoneType', SettingChoiceType::class,
				array(
					'label'        => 'phone.type.label',
					'setting_name' => 'phone.typelist',
				)
			)
			->add('phoneNumber', TextType::class,
				array(
					'label' => 'phone.number.label',
					'attr'  => array(
						'help' => 'phone.number.help',
					),
				)
			)
			->add('countryCode', SettingChoiceType::class,
				array(
					'label'        => 'phone.country.label',
					'required'     => false,
					'setting_name' => 'phone.countrylist',
				)
			);
		$builder->get('phoneNumber')
			->addModelTransformer(new PhoneTransformer());
		$builder->addEventSubscriber($this->phoneSubscriber);
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			array(
				'data_class'         => Phone::class,
				'translation_domain' => 'Phone',
			)
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'phone';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return 'phone';
	}


}
