<?php
namespace App\Menu\Util;


class MenuManagerConstants
{
	CONST NODES = '
1:
	name: System
	label: menu.admin.node
	role: ROLE_USER
	order: 1
	menu: 1
2:
    name: People
    label: menu.people.title
    role: ROLE_USER
    order: 2
    menu: 2
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
12:
    label: menu.timetable.manage
    name: Timatable Management
    role: ROLE_REGISTRAR
    node: 1
    order: 11
    route: course_list
    translate: School
20:
    label: menu.people.manage
    name: People Admin
    role: ROLE_ADMIN
    node: 2
    order: 20
    route: person_manage
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
    groupings:
        department_edit:
            label: menu.school.department.edit
            role: ROLE_REGISTRAR
            route: department_edit
            params:
                id: Add
        house_edit:
            label: menu.setting.houses
            role: ROLE_REGISTRAR
            route: houses_edit
            params: {}
        manage_year_groups:
            label: menu.calendar.groups
            role: ROLE_REGISTRAR
            route: calendar_edit
            params:
                id: current
                _fragment: calendarGroups
        manage_roll_groups:
            label: menu.roll.groups
            role: ROLE_REGISTRAR
            route: roll_list
            params: {}
            translate: School
    others:
        campus_manage:
	        label: menu.campus.manage.title
	        name: Campus Management
	        role: ROLE_REGISTRAR
	        route: campus_manage
	        params: {}
	        translate: Facility
        space_list:
	        label: menu.space.list.title
	        name: Space Management
	        role: ROLE_REGISTRAR
	        route: space_list
	        params: {}
	        translate: Facility
        space_type:
	        label: menu.space.type.title
	        name: Space Management
	        role: ROLE_SYSTEM_ADMIN
	        route: setting_edit_name
	        params:
                name: space.type
                closeWindow: closeWindow
	        translate: Setting
	        target:
                name: Setting_Facility_Type
                options: width=1200,height=900
    
    years_days_times:
        calendar_years:
            label: menu.calendar.manage
            role: ROLE_REGISTRAR
            route: calendar_years
            params: {}
        school_days_times:
            route: school_days_times
            role: ROLE_REGISTRAR
            label: menu.school.daysandtimes
            params: {}
        display_calendar:
            route: calendar_display
            role: ROLE_REGISTRAR
            label: menu.calendar.display
            params: 
                id: current
                closeWindow: closeWindow 
            target:
                name: Calendar
                options: width=1200,height=900
        manage_year_special_days:
            label: menu.calendar.special_days
            role: ROLE_REGISTRAR
            route: calendar_edit
            params:
                id: current
                _fragment: specialDays
        manage_year_terms:
            label: menu.calendar.terms
            role: ROLE_REGISTRAR
            route: calendar_edit
            params:
                id: current
                _fragment: terms
    hidden:
        - calendar_edit
        - edit_grade
        - student_add_to_calendar_group
        - space_edit
        - roll_edit
Person Admin:
    people_manage:
#        family_manage:
#            route: family_manage
#            label: menu.people.family.manage
#            role: ROLE_ADMIN
#            params: { }
        person_manage:
            route: person_manage
            label: menu.people.manage
            role: ROLE_ADMIN
            params: { }
    hidden:
        - person_edit
        - user_manage
        - student_manage
        - staff_manage
        - family_edit
';
}
