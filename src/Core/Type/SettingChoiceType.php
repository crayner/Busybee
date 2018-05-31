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
			)
		);
		$resolver->setDefaults(
			array(
				'expanded'                  => false,
				'multiple'                  => false,
				'placeholder'               => null,
				'calendar_data'             => null,
				'setting_data_name'         => null,
				'setting_display_name'      => null,
				'translation_prefix'        => true,
                'sort_choice'               => true,
                'use_lower_case'            => false,
                'strict_validation'         => false,
                'extra_choices'             => [],
                'validation_translation'    => null,
                'validation_off'            => false,
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
		$view->vars['setting_name']         = $options['setting_name'];
		$view->vars['setting_display_name'] = $options['setting_display_name'];
		$view->vars['setting_data_name']    = $options['setting_data_name'];
        $view->vars['setting_data_value']   = $options['setting_data_value'];
        $view->vars['use_lower_case']       = $options['use_lower_case'];
        $view->vars['strict_validation']    = $options['strict_validation'];
        $view->vars['extra_choices']        = $options['extra_choices'];
        $view->vars['validation_translation']   = $options['validation_translation'];
        $view->vars['validation_off']   = $options['validation_off'];
	}

	/**
	 * @return string
	 */
	public function getParent()
	{
		return ChoiceType::class;
	}
}