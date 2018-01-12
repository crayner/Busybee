<?php
namespace App\Core\Type;

use App\Core\Manager\SettingManager;
use App\Core\Subscriber\SettingChoiceSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SettingChoiceType extends AbstractType
{
	/**
	 * @var SettingChoiceSubscriber
	 */
	private $settingChoiceSubscriber;

	/**
	 * SettingType constructor.
	 *
	 * @param SettingManager $settingManager
	 */
	public function __construct(SettingChoiceSubscriber $settingChoiceSubscriber)
	{
		$this->settingChoiceSubscriber = $settingChoiceSubscriber;
	}

	/**
	 * @return string
	 */
	public function getBlockPrefix()
	{
		return 'setting_choice';
	}

	/**
	 * @param OptionsResolver $resolver
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setRequired(
			array(
				'setting_name',
				'setting_display_name',
			)
		);
		$resolver->setDefaults(
			array(
				'expanded'           => false,
				'multiple'           => false,
				'placeholder'        => null,
				'year_data'          => null,
				'use_label_as_value' => false,
				'setting_data_name'  => null,
				'setting_data_value' => null,
			)
		);
	}

	/**
	 * @param FormBuilderInterface $builder
	 * @param array                $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->addEventSubscriber($this->settingChoiceSubscriber);
	}

	/**
	 * @param FormView      $view
	 * @param FormInterface $form
	 * @param array         $options
	 */
	public function buildView(FormView $view, FormInterface $form, array $options)
	{
		$view->vars['setting_name']       = $options['setting_name'];
		$view->vars['setting_display_name']       = $options['setting_display_name'];
		$view->vars['use_label_as_value'] = $options['use_label_as_value'];
		$view->vars['setting_data_name']  = $options['setting_data_name'];
		$view->vars['setting_data_value'] = $options['setting_data_value'];
	}

	/**
	 * @return string
	 */
	public function getParent()
	{
		return ChoiceType::class;
	}
}