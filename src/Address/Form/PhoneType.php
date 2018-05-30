<?php
namespace App\Address\Form;

use App\Address\Form\Transformer\PhoneTransformer;
use App\Address\Form\Subscriber\PhoneSubscriber;
use App\Core\Type\SettingChoiceType;
use Hillrange\Form\Type\TextType;
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
			->add('phoneNumber', TextType::class,
				[
					'label' => 'phone.number.label',
					'help' => 'phone.number.help',
				]
			)
			->add('countryCode', SettingChoiceType::class,
				[
					'label'        => 'phone.country.label',
					'required'     => false,
					'setting_name' => 'phone.country.list',
					'translation_prefix' => false,
                    'placeholder' => 'phone.country.placeholder',
				]
			)
            ->add('phoneType', SettingChoiceType::class,
                [
                    'label'        => 'phone.type.label',
                    'setting_name' => 'phone.typelist',
                    'placeholder' => 'phone.type.placeholder',
                ]
            )
        ;
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
				'translation_domain' => 'Person',
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
