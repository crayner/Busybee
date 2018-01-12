<?php
namespace App\Core\Form;

use App\Entity\Setting;
use App\Repository\SettingRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class SettingType extends AbstractType
{
	/**
	 * @var SettingRepository
	 */
	private $repo;

	/**
	 * SettingType constructor.
	 *
	 * @param SettingRepository $repo
	 */
	public function __construct(SettingRepository $repo)
	{
		$this->repo = $repo;
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('type', HiddenType::class)
			->add('name', HiddenType::class)
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
	}

	/**
	 * @return array
	 */
	private function getSettingNameChoices()
	{
		$names    = [];
		$settings = $this->repo->findBy([], ['name' => 'ASC']);
		foreach ($settings as $setting)
			$names[$setting->getName()] = $setting->getId();

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
}
