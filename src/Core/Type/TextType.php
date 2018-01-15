<?php
namespace App\Core\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TextType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			[
				'compound' => false,
			]
		);
	}

	public function getParent()
	{
		return \Symfony\Component\Form\Extension\Core\Type\TextType::class;
	}

	public function getBlockPrefix()
	{
		return 'bee_text';
	}
}