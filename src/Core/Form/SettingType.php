<?php
namespace App\Core\Form;

use App\Core\Form\Transform\SettingToStringTransformer;
use App\Core\Subscriber\SettingSubscriber;
use Doctrine\ORM\EntityRepository;
use Hillrange\Form\Type\EntityType;
use Hillrange\Form\Type\TextType;
use App\Entity\Setting;
use App\Repository\SettingRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class SettingType extends AbstractType
{
	/**
	 * @var SettingRepository
	 */
	private $repo;

	/**
	 * @var SettingSubscriber
	 */
	private $settingSubscriber;

	/**
	 * SettingType constructor.
	 *
	 * @param SettingRepository $repo
	 */
	public function __construct(SettingRepository $repo, SettingSubscriber $settingSubscriber)
	{
		$this->repo = $repo;
		$this->settingSubscriber = $settingSubscriber;
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('type', HiddenType::class)
			->add('name', TextType::class,
				[
					'label' => 'system.setting.name.label',
					'help' => 'system.setting.name.help',
					'attr' => [
						'readonly' => true,
					]
				]
			)
			->add('nameSelect', EntityType::class,
				array(
					'label' => '',
					'placeholder' => 'system.setting.name.placeholder',
					'attr' => array(
						'class' => 'changeRecord form-control-sm',
					),
					'mapped' => false,
                    'choice_label' => 'displayName',
                    'class' => Setting::class,
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('s')
                            ->where('s.type != :system')
                            ->setParameter('system', 'system')
                            ->orderBy('s.displayName', 'ASC');
                    },
                    'required' => false,
				)
			)
            ->add('displayName', null,
                array(
                    'label' => 'system.setting.displayName.label',
                    'help'  => 'system.setting.displayName.help',
                    'attr'  => array(
                        'class' => 'changeSetting',
                    )
                )
            )
            ->add('description', TextareaType::class,
                array(
                    'label' => 'system.setting.description.label',
                    'help'  => 'system.setting.description.help',
                    'attr'  => array(
                        'rows'  => '5',
                        'class' => 'changeSetting',
                    )
                )
            )
            ->add('choice', EntityType::class,
                array(
                    'label' => 'system.setting.choice.label',
                    'help'  => 'system.setting.choice.help',
                    'class' => Setting::class,
                    'choice_label' => 'displayName',
                    'choice_value' => 'name',
                    'placeholder' => 'system.setting.choice.placeholder',
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('s')
                            ->where('s.type = :arrayOnly')
                            ->setParameter('arrayOnly', 'array')
                            ->orderBy('s.displayName', 'ASC')
                        ;
                    },
                    'attr'  => [
                        'class' => 'changeSetting',
                    ],
                    'required' => false,
                )
            )
            ->add('validator', TextType::class,
                array(
                    'label' => 'system.setting.validator.label',
                    'help'  => 'system.setting.validator.help',
                    'attr'  => [
                        'class' => 'changeSetting',
                    ],
                    'required' => false,
                )
            )
        ;
		$builder->addEventSubscriber($this->settingSubscriber);
		$builder->get('choice')->addModelTransformer(new SettingToStringTransformer());
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			array(
				'data_class'            => Setting::class,
				'translation_domain'    => 'System',
				'cancelURL'             => null,
                'allow_extra_fields'    => true,
			)
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'setting';
	}

	public function buildView(FormView $view, FormInterface $form, array $options)
	{
		$view->vars['cancelURL'] = $options['cancelURL'];
	}
}
