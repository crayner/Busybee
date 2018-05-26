<?php
namespace App\Core\Validator;

use App\Core\Validator\Constraints\LogoValidator;
use Symfony\Component\Validator\Constraints\Image;

class Logo extends Image
{
	public $mimeTypes = ['image/png', 'image/gif', 'image/jpg', 'image/jpeg'];
	public $minWidth = 250;
	public $maxSize = '750k';
	public $allowSquare = true;
	public $allowLandscape = true;
	public $allowPortrait = true;
	public $detectCorrupted = true;
	public $minRatio = 0.69;
	public $maxRatio = 1.45;

	public function validatedBy()
	{
		return LogoValidator::class;
	}
}
