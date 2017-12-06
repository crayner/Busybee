<?php
namespace App\Core\Type;

use Symfony\Component\Form\Extension\Core\Type\TextType as BaseType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TextType extends BaseType
{
	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'compound' => false,
		));
	}
}