<?php
namespace App\Core\Definition;

interface CanonicaliserInterface
{
	/**
	 * @param string $string
	 *
	 * @return string
	 */
	public function canonicalise($string);
}
