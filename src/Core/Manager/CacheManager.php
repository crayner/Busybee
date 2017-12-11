<?php
namespace App\Core\Manager;

use Symfony\Component\Filesystem\Filesystem;

class CacheManager
{
	/**
	 * @var string
	 */
	private $cacheDir;

	/**
	 * Cache Manager constructor.
	 */
	public function __construct($cacheDir)
	{
		$this->cacheDir = $cacheDir;
	}

	/**
	 * Clear Cache
	 */
	public function clearCache()
	{
		$fs = new Filesystem();
		$fs->remove($this->cacheDir);
	}
}