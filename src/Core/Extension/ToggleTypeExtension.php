<?php
namespace App\Core\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ToggleTypeExtension extends AbstractTypeExtension
{
	public function buildView(FormView $view, FormInterface $form, array $options)
	{
		$view->vars = array_replace($view->vars,
			array(
				'div_class' => $options['div_class'],
			)
		);
		$view->vars['use_toggle'] = $options['use_toggle'];
		$view->vars['button_class_off'] = $options['button_class_off'];
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			array(
				'value'      => '1',
				'compound'   => false,
				'required'   => false,
				'div_class'  => 'toggleRight',
				'use_toggle' => false,
				'button_class_off' => 'btn btn-danger halflings halflings-thumbs-down',
				'button_toggle_on' => [
					'btn-danger' => 'btn-success',
					'halflings-thumbs-down' => 'halflings-thumbs-up'
				],
			)
		);
	}

	/**
	 * @return string
	 */
	public function getExtendedType()
	{
		return CheckboxType::class;
	}
}