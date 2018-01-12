<?php
namespace App\Core\Validator;

use App\Core\Validator\Constraints\BackgroundImageValidator;
use Symfony\Component\Validator\Constraints\Image;

class BackgroundImage extends Image
{
	public $minWidth = 1200;
	public $maxSize = '1024k';
	public $allowSquare = false;
	public $allowLandscape = true;
	public $allowPortrait = false;
	public $detectCorrupted = true;

	/**
	 * @return string
	 */
	public function validatedBy()
	{
		return BackgroundImageValidator::class;
	}
}
