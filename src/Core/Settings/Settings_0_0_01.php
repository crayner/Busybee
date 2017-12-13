<?php
namespace App\Core\Settings;

use App\Core\Definition\SettingInterface;

class Settings_0_0_01 implements SettingInterface
{
	const VERSION = '0.0.01';

	/**
	 * @return string
	 */
	public function getSettings()
	{
		return <<<LLL
version:
    type: system
    value: '0.0.01'
    displayName: 'System Version'
    description: 'The version of Busybee currently configured on your system.'
    role: 'ROLE_SYSTEM_ADMIN'
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