<?php

namespace App\Core\Manager;

use App\Core\Definition\CanonicaliserInterface;

class Canonicaliser implements CanonicaliserInterface
{
	public function canonicalise($string)
	{
		return null === $string ? null : mb_convert_case($string, MB_CASE_LOWER, mb_detect_encoding($string));
	}
}
