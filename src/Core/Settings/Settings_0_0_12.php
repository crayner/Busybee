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
    value: 0.0.12
external.activity.status.list:
    type: array
    displayName: External Activity Status List
    description: Stati applied to external activity.
    role: 'ROLE_SYSTEM_ADMIN'
    value: 
        accepted: accepted
        panding: pending
        waiting_list: waiting_list
        not_accepted: not_accepted
    defaultValue: pending
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