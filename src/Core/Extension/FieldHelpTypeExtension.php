<?php
namespace App\Core\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FieldHelpTypeExtension extends AbstractTypeExtension
{
	public function buildView(FormView $view, FormInterface $form, array $options)
	{
		$view->vars['help'] = $options['help'];
		$view->vars['help_params'] = $options['help_params'];
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			[
				'help'  => null,
				'help_params' => [],
			]
		);
	}

	public function getExtendedType()
	{
		return FormType::class;
	}
}