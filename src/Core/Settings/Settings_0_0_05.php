<?php
namespace App\Core\Settings;

use App\Core\Definition\SettingInterface;

class Settings_0_0_05 implements SettingInterface
{
	const VERSION = '0.0.05';

	/**
	 * @return string
	 */
	public function getSettings()
	{
		return <<<LLL
version:
    value: 0.0.05
activity.provider.type:
    type: array
    displayName: Activity Provider List
    description: Activity Provider List - The name will be translated.
    role: 'ROLE_REGISTRAR'
    value:
        school: school
        external: external
    choice: school
activity.type.type:
    type: array
    displayName: Activity Type List
    description: Activity Type List - The name will be translated.
    role: 'ROLE_REGISTRAR'
    value:
        action: action
        creativity: creativity
        service: service
activity.payment.type:
    type: array
    displayName: Activity Payment Type List
    description: Activity Payment Type List - The name will be translated.
    role: 'ROLE_REGISTRAR'
    choice: program
    value:
        program: program
        session: session
        week: week
        term: term
activity.payment.firmness:
    type: array
    displayName: Activity Payment Firmness List
    description: Activity Payment Firmness List - The name will be translated.
    role: 'ROLE_REGISTRAR'
    choice: finalised
    value:
        finalised: finalised
        estimated: estimated
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