<?php
namespace App\Core\Form;

use App\Core\Subscriber\SettingSubscriber;
use App\Core\Type\TextType;
use App\Entity\Setting;
use App\Repository\SettingRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
			->add('nameSelect', ChoiceType::class,
				array(
					'label'       => '',
					'placeholder' => 'system.setting.name.placeholder',
					'choices'     => $this->getSettingNameChoices(),
					'attr'        => array(
						'class' => 'changeRecord form-control-sm',
					),
					'mapped'      => false,
					'data'        => $options['data']->getNameSelect(),
				)
			)
			->add('displayName', null,
				array(
					'label' => 'system.setting.displayName.label',
					'attr'  => array(
						'help'  => 'system.setting.displayName.help',
						'class' => 'changeSetting',
					)
				)
			)
			->add('description', TextareaType::class,
				array(
					'label' => 'system.setting.description.label',
					'attr'  => array(
						'help'  => 'system.setting.description.help',
						'rows'  => '5',
						'class' => 'changeSetting',
					)
				)
			);
		$builder->addEventSubscriber($this->settingSubscriber);
	}

	/**
	 * @return array
	 */
	private function getSettingNameChoices()
	{
		$names    = [];
		$settings = $this->repo->findBy([], ['name' => 'ASC']);
		foreach ($settings as $setting)
			$names[$setting->getDisplayName()] = $setting->getId();

		return $names;
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			array(
				'data_class'         => Setting::class,
				'translation_domain' => 'System',
				'cancelURL'         => null,
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
