<?php
namespace App\Core\Form\Transform;

use App\Entity\Setting;
use Symfony\Component\Form\DataTransformerInterface;

class SettingToStringTransformer implements DataTransformerInterface
{
	/**
	 * @param mixed $entity
	 *
	 * @return string|array
	 */
	public function transform($entity)
	{
	    if (empty($entity))
	        return null;
	}

	/**
	 * @param mixed $id
	 *
	 * @throws \Symfony\Component\Form\Exception\TransformationFailedException
	 *
	 * @return mixed|object
	 */
	public function reverseTransform($value)
	{
	    if ($value instanceof Setting)
	        return $value->getName();

	    if (empty($value))
	        return null;
	}
}