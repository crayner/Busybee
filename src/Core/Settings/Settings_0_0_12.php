<?php
namespace App\Core\Settings;

use App\Core\Definition\SettingInterface;
use Symfony\Component\Validator\Constraints\CurrencyValidator;

class Settings_0_0_12 implements SettingInterface
{
	const VERSION = '0.0.12';

	/**
	 * @return string
	 */
	public function getSettings()
	{
		return <<<LLL
version:
    type: system
    value: '0.0.12'
    displayName: 'System Version'
    description: 'The version of Busybee currently configured on your system.'
external.activity.status.list:
    type: array
    displayName: External Activity Status List
    description: Status applied to external activity.
    role: 'ROLE_SYSTEM_ADMIN'
    value: 
        - accepted
        - pending
        - waiting_list
        - not_accepted
    defaultValue: pending
external.activity.type.list:
    type: array
    displayName: External Activity Type List
    description: Type of external activity.
    role: 'ROLE_SYSTEM_ADMIN'
    value: 
        - creative
        - action
        - service
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