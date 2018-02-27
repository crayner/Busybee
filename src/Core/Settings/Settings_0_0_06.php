<?php
namespace App\Core\Settings;

use App\Core\Definition\SettingInterface;

class Settings_0_0_06 implements SettingInterface
{
	const VERSION = '0.0.06';

	/**
	 * @return string
	 */
	public function getSettings()
	{
		return <<<LLL
version:
    value: 0.0.06
LLL;
	}

	/**
	 * @return string
	 */
	public function getClassName()
	{
		return get_class();
	}
}