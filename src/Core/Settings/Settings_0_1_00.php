<?php
namespace App\Core\Settings;

use App\Core\Definition\SettingInterface;

class Settings_0_1_00 implements SettingInterface
{
	const VERSION = '0.1.00';

	/**
	 * @return string
	 */
	public function getSettings()
	{
		return <<<LLL
address.format:
    type: twig
    name: address.format
    displayName: 'Address Format'
    description: 'A template for displaying an address.'
    value: |
        <pre>{% if propertyName is not empty %}{{ propertyName }}
        {% endif %}{% if buildingType is not empty %}{{ buildingType }} {% endif %}{% if buildingNumber is not empty %}{{ buildingNumber }}/{% endif %}{% if streetNumber is not empty %}{{ streetNumber }} {% endif %}{{ streetName }}
        {{ locality }} {{ territory }} {{ postCode }}
        {{ country }}</pre>
    choice: null
    validator: null
    role: ROLE_REGISTRAR
    defaultValue: |
        <pre>{% if propertyName is not empty %}{{ propertyName }}
        {% endif %}{% if buildingType is not empty %}{{ buildingType }} {% endif %}{% if buildingNumber is not empty %}{{ buildingNumber }}/{% endif %}{% if streetNumber is not empty %}{{ streetNumber }} {% endif %}{{ streetName }}
        {{ locality }} {{ territory }} {{ postCode }}
        {{ country }}</pre>
    translateChoice: null
address.listlabel:
    type: twig
    name: address.listlabel
    displayName: 'Address Label List'
    description: 'A template to convert the entity values into a string label for autocomplete.'
    value: '{% if buildingType is not empty %}{{ buildingType }} {% endif %}{% if buildingNumber is not empty %}{{ buildingNumber}}/{% endif %}{% if streetNumber is not empty %}{{ streetNumber}} {% endif %}{{ streetName }} {{ locality }}'
    choice: null
    validator: null
    role: ROLE_ADMIN
    defaultValue: null
    translateChoice: null
person.genderlist:
    type: array
    name: person.genderlist
    displayName: 'Gender List'
    description: 'A list of genders used in the system.'
    value:
        - u
        - m
        - f
        - o
    choice: null
    validator: null
    role: ROLE_REGISTRAR
    defaultValue: {  }
    translateChoice: null
person.titlelist:
    type: array
    name: person.titlelist
    displayName: 'List of Titles'
    description: 'List of personal titles used in the system.'
    value:
        - mr
        - mrs
        - ms
        - master
        - miss
        - dr
        - rev
    choice: null
    validator: null
    role: ROLE_REGISTRAR
    defaultValue: {  }
    translateChoice: null
address.territorylist:
    type: array
    name: address.territorylist
    displayName: 'Territory List'
    description: 'List of Territories, States, Provinces or Counties available to addresses in your organisation.'
    value:
        'Not Specified': '@@'
        'New South Wales': NSW
        Victoria: VIC
        Queensland: QLD
        'South Australia': SA
        'Western Australia': WA
        Tasmania: TAS
        'Northern Territory': NT
        'Australian Capital Territory': ACT
        'Overseas Australian Territory': OAT
        Overseas: OS
    choice: null
    validator: null
    role: ROLE_REGISTRAR
    defaultValue: {  }
    translateChoice: null
address.buildingtype:
    type: array
    name: address.buildingtype
    displayName: 'Dcarding Type'
    description: 'List of building types used as dcardings found in your organisation''s area.'
    value:
        - ''
        - Flat
        - Unit
        - Apt
        - THse
    choice: null
    validator: null
    role: ROLE_REGISTRAR
    defaultValue: {  }
    translateChoice: null
phone.typelist:
    type: array
    name: phone.typelist
    displayName: 'Types of Phones'
    description: 'List of phone types.'
    value:
        - home
        - mobile
        - work
        - imported
    choice: null
    validator: null
    role: ROLE_REGISTRAR
    defaultValue: {  }
    translateChoice: null
phone.country.list:
    type: array
    name: phone.country.list
    displayName: 'List of Phone Country Codes'
    description: 'List of phone country codes.'
    value:
        'Australia +61': '+61'
        'Canada +1': '+1'
        'Hong Kong +852': '+852'
        'United Kingdom +44': '+44'
        'USA +1': '+1'
    choice: null
    validator: null
    role: ROLE_SYSTEM_ADMIN
    defaultValue: {  }
    translateChoice: null
phone.validation:
    type: regex
    name: phone.validation
    displayName: 'Phone Validation Rule'
    description: 'Phone Validation Regular Expression'
    value: '/(^(1300|1800|1900|1902)[0-9]{6}$)|(^0[2|3|4|7|8]{1}[0-9]{8}$)|(^13[0-9]{4}$)/'
    choice: null
    validator: null
    role: ROLE_SYSTEM_ADMIN
    defaultValue: '/^([0-9]){6,12}$/'
    translateChoice: null
phone.display:
    type: twig
    name: phone.display
    displayName: 'Phone Display Format'
    description: 'A template to convert phone numbers into display version.'
    value: '{% set start = phone|slice(0,2) %} {% set len = phone|length %} {% if start in [02,03,07,08,09] %} ({{ phone|slice(0,2)}}) {{ phone|slice(2,4)}} {{ phone|slice(6,4)}} {% elseif start in [18,13,04] and len == 10 %} {{ phone|slice(0,4)}} {{ phone|slice(4,3)}} {{ phone|slice(7,3)}} {% elseif start in [13] and len == 6 %} {{ phone|slice(0,2)}} {{ phone|slice(2)}} {% else %} {{ phone }} {% endif %}'
    choice: null
    validator: null
    role: ROLE_REGISTRAR
    defaultValue: null
    translateChoice: null
org.name:
    type: array
    name: org.name
    displayName: 'Organisation Name'
    description: 'The name of your organisation'
    value:
        long: 'Busybee Learning'
        short: BEE
    choice: null
    validator: null
    role: ROLE_REGISTRAR
    defaultValue: {  }
    translateChoice: null
org.ext.id:
    type: string
    name: org.ext.id
    displayName: 'Organisation External Identifier'
    description: 'The identifier given to your organisation by your parent or external education authority.'
    value: ''
    choice: null
    validator: null
    role: ROLE_REGISTRAR
    defaultValue: null
    translateChoice: null
org.postal.address.1:
    type: text
    name: org.postal.address.1
    displayName: 'Organisation Postal Address Line 1'
    description: 'First line of this organisation''s postal address.'
    value: ''
    choice: null
    validator: null
    role: ROLE_REGISTRAR
    defaultValue: null
    translateChoice: null
org.postal.address.2:
    type: text
    name: org.postal.address.2
    displayName: 'Organisation Postal Address Line 2'
    description: 'Second line of this organisation''s postal address.'
    value: ''
    choice: null
    validator: null
    role: ROLE_REGISTRAR
    defaultValue: null
    translateChoice: null
org.postal.locality:
    type: text
    name: org.postal.locality
    displayName: 'Organisation Postal Locality'
    description: 'Locality of this organisation''s postal address. : Town, Suburb or Locality)'
    value: ''
    choice: null
    validator: null
    role: ROLE_REGISTRAR
    defaultValue: null
    translateChoice: null
org.postal.postcode:
    type: string
    name: org.postal.postcode
    displayName: 'Organisation Postal Post Code'
    description: 'Post Code of this organisation''s postal address.'
    value: ''
    choice: null
    validator: null
    role: ROLE_REGISTRAR
    defaultValue: null
    translateChoice: null
org.postal.territory:
    type: enum
    name: org.postal.territory
    displayName: 'Organisation Postal Territory'
    description: 'Territory of this organisation''s postal address. : State, Province, County)'
    value: ''
    choice: Address.TerritoryList
    validator: null
    role: ROLE_REGISTRAR
    defaultValue: null
    translateChoice: null
org.contact.name:
    type: text
    name: org.contact.name
    displayName: 'Organisation Contact'
    description: 'The name of the person to contact in this organisation.'
    value: ''
    choice: null
    validator: null
    role: ROLE_REGISTRAR
    defaultValue: null
    translateChoice: null
org.contact.phone:
    type: string
    name: org.contact.phone
    displayName: 'Organisation Contact Phone Number'
    description: 'The phone number of the person to contact in this organisation.'
    value: ''
    choice: null
    validator: phone.validator
    role: ROLE_REGISTRAR
    defaultValue: null
    translateChoice: null
org.contact.facsimile:
    type: string
    name: org.contact.facsimile
    displayName: 'Organisation Contact Facsimile Number'
    description: 'The facsimile number of the person to contact in this organisation.'
    value: ''
    choice: null
    validator: phone.validator
    role: ROLE_REGISTRAR
    defaultValue: null
    translateChoice: null
org.contact.email:
    type: string
    name: org.contact.email
    displayName: 'Organisation Contact Email Address'
    description: 'The email address of the person to contact in this organisation.'
    value: ''
    choice: null
    validator: email.validator
    role: ROLE_REGISTRAR
    defaultValue: null
    translateChoice: null
org.physical.address.1:
    type: text
    name: org.physical.address.1
    displayName: 'Organisation Physical Address Line 1'
    description: 'First line of this organisation''s physical address.'
    value: ''
    choice: null
    validator: null
    role: ROLE_REGISTRAR
    defaultValue: null
    translateChoice: null
org.physical.address.2:
    type: text
    name: org.physical.address.2
    displayName: 'Organisation Physical Address Line 2'
    description: 'Second line of this organisation''s physical address.'
    value: ''
    choice: null
    validator: null
    role: ROLE_REGISTRAR
    defaultValue: null
    translateChoice: null
org.physical.locality:
    type: text
    name: org.physical.locality
    displayName: 'Organisation Physical Locality'
    description: 'Locality of this organisation''s physical address. : Town, Suburb or Locality)'
    value: ''
    choice: null
    validator: null
    role: ROLE_REGISTRAR
    defaultValue: null
    translateChoice: null
org.physical.postcode:
    type: string
    name: org.physical.postcode
    displayName: 'Organisation Physical Post Code'
    description: 'Post Code of this organisation''s physical address.'
    value: ''
    choice: null
    validator: null
    role: ROLE_REGISTRAR
    defaultValue: null
    translateChoice: null
org.physical.territory:
    type: string
    name: org.physical.territory
    displayName: 'Organisation Physical Territory'
    description: 'Territory of this organisation''s physical address. : State, Province, County)'
    value: ''
    choice: Address.TerritoryList
    validator: null
    role: ROLE_REGISTRAR
    defaultValue: null
    translateChoice: null
countrytype:
    type: text
    name: countrytype
    displayName: 'Country Type Form Handler'
    description: 'Determines how the country details are obtained and stored in the database.'
    value: Symfony\Component\Form\Extension\Core\Type\CountryType
    choice: null
    validator: null
    role: ROLE_SYSTEM_ADMIN
    defaultValue: null
    translateChoice: null
firstdayofweek:
    type: string
    name: firstdayofweek
    displayName: 'First Day of Week'
    description: 'The first day of the week for display purposes.  Monday or Sunday, defaults to Monday.'
    value: Sunday
    choice: null
    validator: null
    role: ROLE_REGISTRAR
    defaultValue: null
    translateChoice: null
schoolweek:
    type: array
    name: schoolweek
    displayName: 'Days in the School Week'
    description: 'Defines the list of days that school would normally be open.'
    value:
        Monday: Mon
        Tuesday: Tue
        Wednesday: Wed
        Thursday: Thu
        Friday: Fri
    choice: null
    validator: null
    role: ROLE_ADMIN
    defaultValue: {  }
    translateChoice: null
org.logo:
    type: image
    name: org.logo
    displayName: 'Organisation Logo'
    description: 'The organisation Logo'
    value: ''
    choice: null
    validator: App\Core\Validator\Logo
    role: ROLE_ADMIN
    defaultValue: img/bee.png
    translateChoice: null
org.transparent.logo:
    type: image
    name: org.transparent.logo
    displayName: 'Organisation Transparent Logo'
    description: 'The organisation Logo in a transparent form.  Recommended to be 80% opacity. Only PNG or GIF image formats support transparency.'
    value: ''
    choice: null
    validator: App\Core\Validator\Logo
    role: ROLE_ADMIN
    defaultValue: img/bee-transparent.png
    translateChoice: null
background.image:
    type: image
    name: background.image
    displayName: 'Background Image'
    description: 'Change the background displayed for the site.  The image needs to be a minimum of 1200px width.  You can load an image of 1M size, but the smaller the size the better.'
    value: ''
    choice: null
    validator: App\Core\Validator\BackgroundImage
    role: ROLE_ADMIN
    defaultValue: img/backgroundPage.jpg
    translateChoice: null
schoolday.open:
    type: time
    name: schoolday.open
    displayName: 'School Day Open Time'
    description: 'At what time are students allowed on campus?'
    value: '07:00'
    choice: null
    validator: null
    role: ROLE_ADMIN
    defaultValue: null
    translateChoice: null
schoolday.begin:
    type: time
    name: schoolday.begin
    displayName: 'School Day Instruction Start Time'
    description: 'The time that teaching starts. Students would normally be considered late after this time.'
    value: '08:15'
    choice: null
    validator: null
    role: ROLE_ADMIN
    defaultValue: null
    translateChoice: null
schoolday.finish:
    type: time
    name: schoolday.finish
    displayName: 'School Day Instruction Finish Time'
    description: 'The time students are released for the day.'
    value: '15:30'
    choice: null
    validator: null
    role: ROLE_ADMIN
    defaultValue: null
    translateChoice: null
schoolday.close:
    type: time
    name: schoolday.close
    displayName: 'School Day Close Time'
    description: 'The time the doors of the campus normally close, all after school and school activities finished.'
    value: '17:00'
    choice: null
    validator: null
    role: ROLE_ADMIN
    defaultValue: null
    translateChoice: null
space.type:
    type: array
    name: space.type
    displayName: 'Type of Space'
    description: 'Spaces are spaces used with the Campus, such as classrooms, purpose built rooms and Storage Rooms.'
    value:
        teaching_space:
            - classroom
            - hall
            - laboratory
            - library
            - other
            - outdoor
            - performance
            - study
            - undercover
        non_teaching_space:
            - 'meeting room'
            - office
            - staff
            - storage
    choice: null
    validator: App\School\Validator\SpaceType
    role: ROLE_ADMIN
    defaultValue: {  }
    translateChoice: null
staff.categories:
    type: array
    name: staff.categories
    displayName: 'Staff Categories'
    description: 'List of the staff Categories.'
    value:
        - unknown
        - teacher
        - ancillary
        - cleaner
        - administrative
    choice: null
    validator: null
    role: ROLE_ADMIN
    defaultValue: {  }
    translateChoice: null
phone.country.code:
    type: enum
    name: phone.country.code
    displayName: 'Phone Country Code'
    description: 'Default phone country code.'
    value: ''
    choice: phone.country.list
    validator: null
    role: ROLE_SYSTEM_ADMIN
    defaultValue: null
    translateChoice: null
person.import:
    type: array
    name: person.import
    displayName: 'Person Import Defaults'
    description: 'Default values added to imported records.'
    value: {  }
    choice: null
    validator: null
    role: ROLE_REGISTRAR
    defaultValue: {  }
    translateChoice: null
phone.format:
    type: twig
    name: phone.format
    displayName: 'Phone Full Display Format'
    description: 'A template to convert phone numbers into full display version.'
    value: '{{ phoneNumber }}'
    choice: null
    validator: null
    role: ROLE_REGISTRAR
    defaultValue: null
    translateChoice: null
student.caregiver.relationship.list:
    type: array
    name: student.caregiver.relationship.list
    displayName: 'List of Student to Care Giver Relationship'
    description: 'List of Student - Care Giver Relationship'
    value:
        - unknown
        - parent
        - guardian
        - grand_parent
        - family_friend
    choice: null
    validator: null
    role: ROLE_ADMIN
    defaultValue: {  }
    translateChoice: null
ethnicity.list:
    type: array
    name: ethnicity.list
    displayName: 'List of Ethnicities'
    description: 'List of Ethnicities.  Uses the Australian Standard to create this list'
    value:
        'Inadequately described': '0000'
        'Not stated': '0001'
        Eurasian: '0901'
        Asian: '0902'
        'African, so decribed': '00903'
        'European, so decribed': '904'
        'Caucasian, so decribed': '0905'
        'Creole, so decribed': '0906'
        Oceanian: '1000'
        'Australian Peoples': '1100'
        'New Zealand Peoples': '1200'
        'Melanesian and Papuan': '1300'
        Micronesian: '1400'
        Polynesian: '1500'
        'North-West European': '2000'
        British: '2100'
        'Western European': '2300'
        'Northern European': '2400'
        'Southern and  Eastern European': '3000'
        'Southern European': '3100'
        'South Eastern European': '3200'
        'Eastern European': '3300'
        'North African and Middle Eastern': '4000'
        Arab: '4100'
        'Peoples of the Sudan': '4300'
        'Other North African and Middle Eastern': '4900'
        'South-East  Asian': '5000'
        'Mainland South-East Asian': '5100'
        'Maritime South-East Asian': '5200'
        'North-East Asian': '6000'
        'Chinese Asian': '6100'
        'Other North-East Asian': '6900'
        'Southern and Central Asian': '7000'
        'Southern Asian': '7100'
        'Central Asian': '7200'
        'Peoples of the Americas': '8000'
        'North American': '8100'
        'South American': '8200'
        'Central American': '8300'
        'Caribbean Islander': '8400'
        'Sub-Saharan African': '9000'
        'Central and West African': '9100'
        'Southern and East African': '9200'
    choice: null
    validator: null
    role: null
    defaultValue: {  }
    translateChoice: null
religion.list:
    type: array
    name: religion.list
    displayName: 'List of Religions'
    description: 'List of Religions.  Uses the Australian Standard to create this list'
    value:
        'Aboriginal Evangelical Missions': '2801'
        'Acts 2 Alliance': '2416'
        Agnosticism: '7201'
        'Albanian Orthodox': '2231'
        'Ancestor Veneration': '6051'
        'Ancient Church of the East': '2222'
        'Anglican Catholic Church': '2013'
        'Anglican Church of Australia': '2012'
        Animism: '6131'
        'Antiochian Orthodox': '2232'
        'Apostolic Church (Australia)': '2401'
        'Apostolic Church of Queensland': '2901'
        'Armenian Apostolic': '2212'
        'Assyrian Apostolic, nec': '2229'
        'Assyrian Church of the East': '2221'
        Atheism: '7202'
        'Australian Aboriginal Traditional Religions': '6011'
        'Australian Christian Churches (Assemblies of God)': '2402'
        'Baha''i': '6031'
        Baptist: '2031'
        'Bethesda Ministries International (Bethesda Churches)': '2403'
        'Born Again Christian': '2802'
        Brethren: '2051'
        Buddhism: '1011'
        'C3 Global (Christian City Church)': '2404'
        Caodaism: '6991'
        'Catholic, nec': '2079'
        'Chaldean Catholic': '2075'
        'Chinese Religions, nec': '6059'
        Christadelphians: '2902'
        'Christian and Missionary Alliance': '2803'
        'Christian Church in Australia': '2417'
        'Christian Community Churches of Australia': '2811'
        'Christian Science': '2903'
        'Church of Christ (Nondenominational)': '2112'
        'Church of Jesus Christ of Latter-day Saints': '2151'
        'Church of Scientology': '6992'
        'Church of the Nazarene': '2804'
        'Churches of Christ (Conference)': '2111'
        'Community of Christ': '2152'
        Confucianism: '6052'
        Congregational: '2805'
        'Coptic Orthodox': '2214'
        'CRC International (Christian Revival Crusade)': '2407'
        Druidism: '6132'
        Druse: '6071'
        'Eastern Orthodox, nec': '2239'
        Eckankar: '6993'
        'Ethiopian Orthodox': '2216'
        'Ethnic Evangelical Churches': '2806'
        'Foursquare Gospel Church': '2411'
        'Free Reformed': '2253'
        'Full Gospel Church of Australia (Full Gospel Church)': '2412'
        'Gnostic Christians': '2904'
        'Grace Communion International (Worldwide Church of God)': '2915'
        'Greek Orthodox': '2233'
        Hinduism: '3011'
        Humanism: '7203'
        'Independent Evangelical Churches': '2807'
        'International Church of Christ': '2113'
        'International Network of Churches (Christian Outreach Centres)': '2406'
        Islam: '4011'
        Jainism: '6997'
        'Japanese Religions, nec': '6119'
        'Jehovah''s Witnesses': '2131'
        Judaism: '5011'
        'Liberal Catholic Church': '2905'
        Lutheran: '2171'
        'Macedonian Orthodox': '2234'
        Mandaean: '6901'
        'Maronite Catholic': '2072'
        'Melkite Catholic': '2073'
        'Methodist, so described': '2812'
        'Multi Faith': '7301'
        'Nature Religions, nec': '6139'
        'New Age': '7302'
        'New Apostolic Church': '2906'
        'New Churches (Swedenborgian)': '2907'
        'No Religion, so described': '7101'
        'Oriental Orthodox, nec': '2219'
        'Other Anglican': '2019'
        'Other Christian, nec': '2999'
        'Other Protestant, nec': '2899'
        'Other Spiritual Beliefs, nec': '7399'
        'Own Spiritual Beliefs': '7303'
        Paganism: '6133'
        'Pentecostal City Life Church': '2418'
        'Pentecostal, nec': '2499'
        Presbyterian: '2251'
        Rastafari: '6994'
        'Ratana (Maori)': '2908'
        Rationalism: '7204'
        Reformed: '2252'
        'Religious Groups, nec': '6999'
        'Religious Science': '2911'
        'Religious Society of Friends (Quakers)': '2912'
        'Revival Centres': '2413'
        'Revival Fellowship': '2421'
        'Rhema Family Church': '2414'
        'Romanian Orthodox': '2235'
        'Russian Orthodox': '2236'
        'Salvation Army': '2271'
        Satanism: '6995'
        'Secular Beliefs nec': '7299'
        'Serbian Orthodox': '2237'
        'Seventh-day Adventist': '2311'
        Shinto: '6111'
        Sikhism: '6151'
        Spiritualism: '6171'
        'Sukyo Mahikari': '6112'
        'Syrian Orthodox': '2215'
        'Syro Malabar Catholic': '2076'
        Taoism: '6053'
        'Temple Society': '2913'
        Tenrikyo: '6113'
        Theism: '7304'
        Theosophy: '6996'
        'Ukrainian Catholic': '2074'
        'Ukrainian Orthodox': '2238'
        'United Methodist Church': '2813'
        'United Pentecostal': '2415'
        'Uniting Church': '2331'
        'Universal Unitarianism': '7305'
        'Victory Life Centre': '2422'
        'Victory Worship Centre': '2423'
        'Wesleyan Methodist Church': '2808'
        'Western Catholic': '2071'
        'Wiccan (Witchcraft)': '6135'
        'Worship Centre network': '2424'
        Yezidi: '6902'
        Zoroastrianism: '6998'
    choice: null
    validator: null
    role: ROLE_ADMIN
    defaultValue: {  }
    translateChoice: null
residency.list:
    type: array
    name: residency.list
    displayName: 'List of Residency Status'
    description: 'List of Residency Status.  Usually defined by the government.'
    value:
        - citizen
        - temporary
        - permanent
        - visitor
    choice: null
    validator: null
    role: ROLE_ADMIN
    defaultValue: {  }
    translateChoice: null
house.list:
    type: array
    name: house.list
    displayName: 'House List'
    description: 'House list used in your school.'
    value:
        ming:
            name: Ming
            shortName: M
            logo: null
        song:
            name: Song
            shortName: S
            logo: null
        tang:
            name: Tang
            shortName: T
            logo: null
    choice: null
    validator: App\School\Validator\Houses
    role: ROLE_REGISTRAR
    defaultValue: {  }
    translateChoice: null
student.groups:
    type: array
    name: student.groups
    displayName: 'Student Groups'
    description: 'Usually the year groups that attend this school.'
    value:
        - Y13
        - Y12
        - Y11
        - Y10
        - Y9
        - Y8
        - Y7
        - Y6
        - Y5
        - Y4
        - Y3
        - Y2
        - Y1
        - Y0
    choice: null
    validator: null
    role: ROLE_REGISTRAR
    defaultValue: {  }
    translateChoice: null
department.type.list:
    type: array
    name: department.type.list
    displayName: 'Department Type List'
    description: 'Types of departments within the institute.'
    value:
        - learning_area
        - administration
    choice: null
    validator: null
    role: ROLE_REGISTRAR
    defaultValue: {  }
    translateChoice: null
department.staff.type.list:
    type: array
    name: department.staff.type.list
    displayName: 'Department Staff Type List'
    description: 'Types of staff within departments within the institute.'
    value:
        learning:
            - coordinator
            - 'assistant_coordinator'''
            - teacher_curriculum
            - teacher
            - other
        administration:
            - director
            - manager
            - administrator
            - other
    choice: null
    validator: null
    role: ROLE_REGISTRAR
    defaultValue: {  }
    translateChoice: null
date.format:
    type: array
    name: date.format
    displayName: 'Date Format'
    description: 'Display the date in reports in this format. Formats are found at http://php.net/manual/en/function.date.php'
    value:
        long: 'D, jS M/Y'
        short: j/m/Y
        widget: dMMMy
    choice: null
    validator: null
    role: ROLE_REGISTRAR
    defaultValue: {  }
    translateChoice: null
time.format:
    type: array
    name: time.format
    displayName: 'Time Format'
    description: 'Display the time in reports in this format. Formats are found at http://php.net/manual/en/function.date.php'
    value:
        long: 'h:i:s a'
        short: 'H:i'
    choice: null
    validator: null
    role: ROLE_REGISTRAR
    defaultValue: {  }
    translateChoice: null
schoolday.periods:
    type: array
    name: schoolday.periods
    displayName: 'Timetable default period definition.'
    description: 'A default set of period times for days in the timetable.  These values are used to seed the timetable, which allows the timing to be changed if necessary.'
    value:
        Registration:
            code: Reg
            start: '08:15'
            end: '08:30'
            type: pastoral
        'Period 1':
            start: '08:30'
            end: '09:40'
            code: P1
        'Period 2':
            start: '09:40'
            end: '10:50'
            code: P2
        'Morning Tea':
            start: '10:50'
            end: '11:10'
            code: MT
            type: break
        'Period 3':
            start: '11:10'
            end: '12:20'
            code: P3
        Lunch:
            start: '12:20'
            end: '13:10'
            code: Lch
            type: break
        'Period 4':
            start: '13:10'
            end: '14:20'
            code: P4
        'Period 5':
            start: '14:20'
            end: '15:30'
            code: P5
    choice: null
    validator: null
    role: ROLE_REGISTRAR
    defaultValue: {  }
    translateChoice: null
languages.translated:
    type: array
    name: languages.translated
    displayName: 'Languages Translated'
    description: 'Languages that have been translated for the system.'
    value:
        - en_AU
        - en_GB
        - en_US
    choice: null
    validator: null
    role: ROLE_REGISTRAR
    defaultValue: {  }
    translateChoice: null
teachingload.timetable.maximum:
    type: integer
    name: teachingload.timetable.maximum
    displayName: 'Timetable Teaching Load Maximum'
    description: 'The maximum number of periods in a timetable cycle that a teacher should have face to face teaching.'
    value: '9'
    choice: null
    validator: null
    role: ROLE_PRINCIPAL
    defaultValue: null
    translateChoice: null
teachingload.column.maximum:
    type: integer
    name: teachingload.column.maximum
    displayName: 'Day Teaching Load Maximum'
    description: 'The maximum number of periods in a timetable day that a teacher should have face to face teaching.'
    value: '3'
    choice: null
    validator: null
    role: ROLE_PRINCIPAL
    defaultValue: '3'
    translateChoice: null
calendar.status.list:
    type: array
    name: calendar.status.list
    displayName: 'Calendar Status List'
    description: 'Calendar Status List - The name will be translated.'
    value:
        - past
        - current
        - future
    choice: null
    validator: null
    role: ROLE_SYSTEM_ADMIN
    defaultValue: {  }
    translateChoice: null
activity.provider.type:
    type: array
    name: activity.provider.type
    displayName: 'Activity Provider List'
    description: 'Activity Provider List - The name will be translated.'
    value:
        - school
        - external
    choice: null
    validator: null
    role: ROLE_REGISTRAR
    defaultValue: {  }
    translateChoice: null
activity.type.type:
    type: array
    name: activity.type.type
    displayName: 'Activity Type List'
    description: 'Activity Type List - The name will be translated.'
    value:
        - action
        - creativity
        - service
    choice: null
    validator: null
    role: ROLE_REGISTRAR
    defaultValue: {  }
    translateChoice: null
activity.payment.type:
    type: array
    name: activity.payment.type
    displayName: 'Activity Payment Type List'
    description: 'Activity Payment Type List - The name will be translated.'
    value:
        - program
        - session
        - week
        - term
    choice: null
    validator: null
    role: ROLE_REGISTRAR
    defaultValue: {  }
    translateChoice: null
activity.payment.firmness:
    type: array
    name: activity.payment.firmness
    displayName: 'Activity Payment Firmness List'
    description: 'Activity Payment Firmness List - The name will be translated.'
    value:
        - finalised
        - estimated
    choice: null
    validator: null
    role: ROLE_REGISTRAR
    defaultValue: {  }
    translateChoice: null
tutor.type.list:
    type: array
    name: tutor.type.list
    displayName: 'Tutor Type List'
    description: 'Tutor Type List - The name will be translated.'
    value:
        - teacher
        - assistant
        - technician
        - parent
        - coach
        - organiser
    choice: null
    validator: null
    role: ROLE_REGISTRAR
    defaultValue: {  }
    translateChoice: null
currency:
    type: string
    name: currency
    displayName: Currency
    description: 'The currency use by the school.'
    value: AUD
    choice: null
    validator: Symfony\Component\Validator\Constraints\CurrencyValidator
    role: ROLE_SYSTEM_ADMIN
    defaultValue: null
    translateChoice: null
google:
    type: array
    name: google
    displayName: 'Google Authentication and App Access'
    description: 'Google Authentication and App Access details.'
    value:
        o_auth: '1'
        client_id: 142820932329-q1upj2vokedceen3nhcp6l8uo6hulsl2.apps.googleusercontent.com
        client_secret: EZ9oJc3uuvHh_2X27lkMexZ-
    choice: null
    validator: null
    role: ROLE_SYSTEM_ADMIN
    defaultValue: {  }
    translateChoice: null
external.activity.status.list:
    type: array
    name: external.activity.status.list
    displayName: 'External Activity Status List'
    description: 'Status applied to external activity.'
    value:
        - accepted
        - pending
        - waiting_list
        - not_accepted
    choice: null
    validator: null
    role: ROLE_SYSTEM_ADMIN
    defaultValue: {  }
    translateChoice: null
external.activity.type.list:
    type: array
    name: external.activity.type.list
    displayName: 'External Activity Type List'
    description: 'Type of external activity.'
    value:
        - creative
        - action
        - service
    choice: null
    validator: null
    role: ROLE_SYSTEM_ADMIN
    defaultValue: {  }
    translateChoice: null
period.type.list:
    type: array
    name: period.type.list
    displayName: 'Period Type List'
    description: 'Define the types of Periods used in your school.'
    value:
        - lesson
        - pastoral
        - sport
        - break
        - service
        - other
    choice: null
    validator: null
    role: ROLE_PRINCIPAL
    defaultValue: {  }
    translateChoice: null
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