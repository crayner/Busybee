<?php
namespace App\Core\Settings;

use App\Core\Definition\SettingInterface;

class Settings_0_0_04 implements SettingInterface
{
	const VERSION = '0.0.04';

	/**
	 * @return string
	 */
	public function getSettings()
	{
		return <<<LLL
version:
    value: 0.0.04
calendar.status.list:
    type: 'array'
    displayName: 'Calendar Status List'
    description: 'Calendar Status List - The name will be translated.'
    role: 'ROLE_SYSTEM_ADMIN'
    value:
        calendar.status.past: past
        calendar.status.current: current
        calendar.status.future: future
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