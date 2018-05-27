<?php
namespace App\Core\Settings;

use App\Core\Definition\SettingInterface;
use Symfony\Component\Validator\Constraints\CurrencyValidator;

class Settings_0_0_09 implements SettingInterface
{
	const VERSION = '0.0.09';

	/**
	 * @return string
	 */
	public function getSettings()
	{
		return <<<LLL
version:
    type: system
    value: '0.0.09'
    displayName: 'System Version'
    description: 'The version of Busybee currently configured on your system.'
currency:
    type: string
    displayName: Currency
    description: The currency use by the school.
    role: 'ROLE_SYSTEM_ADMIN'
    value: AUD
    validator: Symfony\Component\Validator\Constraints\CurrencyValidator
google:
    type: array
    displayName: Google Authentication and App Access
    description: Google Authentication and App Access details.
    role: ROLE_SYSTEM_ADMIN
    value:
        o_auth: 0
        client_id: ''
        client_secret: ''
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