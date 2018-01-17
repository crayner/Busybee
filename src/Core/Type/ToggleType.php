<?php
namespace App\Core\Type;

use App\Core\Type\Transform\ToggleTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ToggleType extends AbstractType
{

	public function getParent()
	{
		return HiddenType::class;
	}

	public function getBlockPrefix()
	{
		return 'bee_toggle';
	}

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
}