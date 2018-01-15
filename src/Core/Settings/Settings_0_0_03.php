<?php
namespace App\Core\Settings;

use App\Core\Definition\SettingInterface;

class Settings_0_0_03 implements SettingInterface
{
	const VERSION = '0.0.03';

	/**
	 * @return string
	 */
	public function getSettings()
	{
		return <<<LLL
version:
    value: 0.0.03
house.list:
    type: 'array'
    displayName: 'House List'
    description: "House list used in your school."
    role: 'ROLE_REGISTRAR'
    value: []
student.groups:
    type: array
    displayName: Student Groups
    description: Usually the year groups that attend this school.
    role: ROLE_REGISTRAR
    value:
        'Year 13': Y13
        'Year 12': Y12
        'Year 11': Y11
        'Year 10': Y10
        'Year 9': Y9
        'Year 8': Y8
        'Year 7': Y7
        'Year 6': Y6
        'Year 5': Y5
        'Year 4': Y4
        'Year 3': Y3
        'Year 2': Y2
        'Year 1': Y1
        'Kinder': Y0
student.enrolment.status:
    type: array
    displayName: Student Enrolment Status
    description: Reflects the status of student enrolment.
    role: ROLE_ADMIN
    value:
        Future: Future
        Current: Current
        Left: Left
        Past: Past
        Archived: Archived
department.type.list:
    type: array
    displayName: Department Type List
    role: ROLE_REGISTRAR
    description: Types of departments within the institute.
    value:
        Learning Area: Learning Area
        Administration: Administration
department.staff.type.list:
    type: array
    displayName: Department Staff Type List
    role: ROLE_REGISTRAR
    description: Types of staff within departments within the institute.
    value:
        Learning:
            Coordinator: Coordinator
            Assistant Coordinator: Assistant Coordinator
            Teacher (Curriculum): Teacher (Curriculum)
            Teacher: Teacher
            Other: Other
        Administation:
            Director: Director
            Manager: Manager
            Administrator: Administrator
            Other: Other
date.format:
    type: array
    value:
        long: D, jS M/Y
        short: j/m/Y
        widget: dMMMy
    displayName: Date Format
    description: Display the date in reports in this format. Formats are found at http://php.net/manual/en/function.date.php
    role: ROLE_REGISTRAR
time.format:
    type: array
    value:
        long: H:i:s
        short: h:i
    displayName: Time Format
    description: Display the time in reports in this format. Formats are found at http://php.net/manual/en/function.date.php
    role: ROLE_REGISTRAR
SchoolDay.Periods:
      type: array
      value:
          Housekeeping:
              start: '09:00'
              end: '09:05'
              abbr: HK
          Period 1:
              start: '09:05'
              end: '09:50'
              abbr: P1
          Period 2:
              start: '09:50'
              end: '10:35'
              abbr: P2
          Morning Tea:
              start: '10:35'
              end: '10:50'
              abbr: MT
          Period 3:
              start: '10:50'
              end: '11:35'
              abbr: P3
          Period 4:
              start: '11:35'
              end: '12:20'
              abbr: P4
          Period 5:
              start: '12:20'
              end: '13:05'
              abbr: P5
          Lunch:
              start: '13:05'
              end: '13:50'
              abbr: Lch
          Period 6:
              start: '13:50'
              end: '14:35'
              abbr: P5
          Period 7:
              start: '14:35'
              end: '15:20'
              abbr: P6
      displayName: Timetable default period definition.
      description: A default set of period times for days in the timetable.  These values are used to seed the timetable, which allows the timing to be changed if necessary.
      role: ROLE_REGISTRAR
languages.translated:
      type: array
      value:
          English (Australia): en_AU
          English (Great Britian): en_GB
          English (United States): en_US
      displayName: Languages Translated
      choice: en_GB
      description: Languages that have been translated for the system.
      role: ROLE_REGISTRAR
teachingload.timetable.maximum:
      type: integer
      value: 9
      displayName: Timetable Teaching Load Maximum
      description: The maximum number of periods in a timetable cycle that a teacher should have face to face teaching.
      role: ROLE_PRINCIPAL
teachingload.column.maximum:
      type: integer
      value: 1
      displayName: Day Teaching Load Maximum
      description: The maximum number of periods in a timetable day that a teacher should have face to face teaching.
      role: ROLE_PRINCIPAL
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