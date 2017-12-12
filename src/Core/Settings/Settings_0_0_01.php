<?php
namespace App\Core\Settings;

class Settings_0_0_01
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
}