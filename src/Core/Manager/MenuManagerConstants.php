<?php
/**
 * Created by PhpStorm.
 * User: craig
 * Date: 3/01/2018
 * Time: 18:10
 */

namespace App\Core\Manager;


class MenuManagerConstants
{
	CONST NODES = '
1:
	name: System
	label: menu.admin.node
	role: ROLE_USER
	order: 1
	menu: 1
9:
    name: Miscellaneous
    label: menu.miscellaneous
    order: 9
    menu: 9
    route: acknowledgement
';

	CONST ITEMS = '
10:
    label: menu.admin.school
    name: School Admin
    role: ROLE_REGISTRAR
    node: 1
    order: 10
    route: calendar_years
11:
    label: menu.setting.manage
    name: Setting Management
    role: ROLE_REGISTRAR
    node: 1
    order: 11
    route: setting_manage
90:
    label: menu.template_design
    name: Template Design
    route: home_template
    parameters: []
    node: 9
    order: 90
91:
    label: menu.acknowledgement
    name: System Acknowledgement
    route: acknowledgement
    parameters: []
    node: 9
    order: 91
';

	CONST SECTIONS = '
System Admin:
    extend_update:
        acknowledgement:
            route: acknowledgement
            label: menu.site.acknowledgement
            role: []
            params: {}
    settings:
        setting_manage:
            label: menu.setting.manage
            role: ROLE_SYSTEM_ADMIN
            route: setting_manage
            params: {}
    hidden:
        - setting_edit
        - setting_edit_name
        - page_edit
School Admin:
    years_days_times:
        calendar_years:
            label: menu.year.manage
            role: ROLE_REGISTRAR
            route: calendar_years
            params: {}
    hidden:
        - year_edit
        - edit_grade
        - student_add_to_calendar_group
';
}
