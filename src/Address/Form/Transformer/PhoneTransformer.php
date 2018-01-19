<?php

namespace App\Address\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;

class PhoneTransformer implements DataTransformerInterface
{
	/**
	 * Does Nothing
	 *
	 * @param  string $phoneNumber
	 *
	 * @return string
	 */
	public function transform($phoneNumber)
	{
		return $phoneNumber;
	}

	/**
	 * Transforms a string by removing all but digits
	 *
	 * @param  string $phoneNumber
	 *
	 * @return string
	 */
	public function reverseTransform($phoneNumber)
	{
		return preg_replace('/\D/', '', $phoneNumber);
	}
}