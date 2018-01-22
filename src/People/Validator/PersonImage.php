<?php
namespace App\People\Validator;

use App\People\Validator\Constraints\PersonImageValidator;
use Symfony\Component\Validator\Constraints\Image;

class PersonImage extends Image
{
	public $minWidth = 350;
	public $maxSize = '300k';
	public $allowSquare = false;
	public $allowLandscape = false;
	public $allowPortrait = true;
	public $detectCorrupted = true;
	public $maxWidth = 450;
	public $minHeight = 400;
	public $maxHeight = 500;
	public $minRatio = 0.75;
	public $maxRatio = 0.9;

	/**
	 * @return string
	 */
	public function validatedBy()
	{
		return PersonImageValidator::class;
	}
}