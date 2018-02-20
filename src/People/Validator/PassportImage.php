<?php
namespace App\People\Validator;

use App\People\Validator\Constraints\PassportImageValidator;
use Symfony\Component\Validator\Constraints\Image;

class PassportImage extends Image
{
	public $minWidth = 1500;
	public $maxSize = '1500k';
	public $allowSquare = false;
	public $allowLandscape = true;
	public $allowPortrait = false;
	public $detectCorrupted = true;
	public $maxWidth = 3000;
	public $minHeight = 1000;
	public $maxHeight = 2000;

	public function validatedBy()
	{
		return PassportImageValidator::class;
	}
}
