<?php
namespace App\Core\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class AutoCompleteType extends AbstractType
{
	/**
	 * @return null|string
	 */
	public function getBlockPrefix()
	{
		return 'auto_complete';
	}

	/**
	 * @return null|string
	 */
	public function getParent()
	{
		return EntityType::class;
	}
}