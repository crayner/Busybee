<?php
namespace App\Core\Settings;

use App\Core\Definition\SettingInterface;
use Symfony\Component\Validator\Constraints\CurrencyValidator;

class Settings_0_0_30 implements SettingInterface
{
	const VERSION = '0.0.30';

	/**
	 * @return string
	 */
	public function getSettings()
	{
		return <<<LLL
version:
    type: system
    value: '0.0.30'
    displayName: 'System Version'
    description: 'The version of Busybee currently configured on your system.'
period.type.list:
    type: array
    displayName: Period Type List
    description: Define the types of Periods used in your school.
    role: 'ROLE_PRINCIPAL'
    value: 
        lesson: 'lesson'
        pastoral: 'pastoral'
        sport: 'sport'
        break: 'break'
        service: 'service'
        other: 'other'
    defaultValue: lesson
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