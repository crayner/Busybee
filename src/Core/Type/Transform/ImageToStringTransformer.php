<?php
namespace App\Core\Type\Transform;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\File;

class ImageToStringTransformer implements DataTransformerInterface
{
	/**
	 * Transforms an string to File
	 *
	 * @param  File|null $data
	 *
	 * @return string
	 */
	public function transform($data): File
	{
		$file = file_exists($data) ? $data : null;
		$file = is_null($file) ? new File('', false) : new File($file, true);

		return $file;
	}

	/**
	 * Transforms a File into a string.
	 *
	 * @param mixed $data
	 *
	 * @return null|string
	 * @internal param $ null|File
	 */
	public function reverseTransform($data)
	{
		return $data;
	}
}