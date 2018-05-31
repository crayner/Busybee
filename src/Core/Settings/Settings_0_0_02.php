<?php
namespace App\Core\Settings;

use App\Core\Definition\SettingInterface;

class Settings_0_0_02 implements SettingInterface
{
	const VERSION = '0.0.02';

	/**
	 * @return string
	 */
	public function getSettings()
	{
		return <<<LLL
version:
    type: system
    value: '0.0.02'
    displayName: 'System Version'
    description: 'The version of Busybee currently configured on your system.'
address.format:
    type: 'twig'
    displayName: 'Address Format'
    description: 'A template for displaying an address.'
    role: 'ROLE_REGISTRAR'
    value: |
        <pre>{% if propertyName is not empty %}{{ propertyName }}
        {% endif %}{% if buildingType is not empty %}{{ buildingType }} {% endif %}{% if buildingNumber is not empty %}{{ buildingNumber}}/{% endif %}{% if streetNumber is not empty %}{{ streetNumber}} {% endif %}{{ streetName }}
        {{ locality }} {{ territory }} {{ postCode }}
        {{ country }}</pre>
Address.ListLabel:
    type: 'twig'
    displayName: 'Address Label List'
    description: 'A template to convert the entity values into a string label for autocomplete.'
    role: 'ROLE_ADMIN'
    value: "{% if buildingType is not empty %}{{ buildingType }} {% endif %}{% if buildingNumber is not empty %}{{ buildingNumber}}/{% endif %}{% if streetNumber is not empty %}{{ streetNumber}} {% endif %}{{ streetName }} {{ locality }}"
person.genderlist:
    type: 'array'
    displayName: 'Gender List'
    description: 'A list of genders used in the system.'
    role: 'ROLE_REGISTRAR'
    value:
        - u
        - m
        - f
        - o
person.titlelist:
    type: 'array'
    displayName: 'List of Titles'
    description: 'List of personal titles used in the system.'
    role: 'ROLE_REGISTRAR'
    value:
        '': ''
        'Mr': 'Mr'
        'Mrs': 'Mrs'
        'Ms': 'Ms'
        'Miss': 'Miss'
        'Dr': 'Dr'
Address.TerritoryList:
    type: 'array'
    displayName: 'Territory List'
    description: 'List of Territories, States, Provinces or Counties available to addresses in your organisation.'
    role: 'ROLE_REGISTRAR'
    value:
        'Not Specified': 'Unknown'
Address.BuildingType:
    type: 'array'
    displayName: 'Dcarding Type'
    description: "List of building types used as dcardings found in your organisation's area."
    role: 'ROLE_REGISTRAR'
    value:
        'Not Specified':  ''
        'Flat': 'Flat'
        'Unit':  'Unit'
        'Apartment':  'Apt'
        'Town House':  'THse'
phone.typelist:
    type: 'array'
    displayName: 'Types of Phones'
    description: "List of phone types. The key : key: value) is displayed on your system, and the value is stored in the database."
    role: 'ROLE_REGISTRAR'
    value:
        - home
        - mobile
        - work
        - imported
phone.country.list:
    type: 'array'
    displayName: 'List of Phone Country Codes'
    description: "List of phone country codes."
    role: 'ROLE_SYSTEM_ADMIN'
    value:
        'Australia +61': '+61'
        'Canada +1': '+1'
        'Hong Kong +852': '+852'
        'USA +1': '+1'
Phone.Validation:
    type: 'regex'
    displayName: 'Phone Validation Rule'
    description: "Phone Validation Regular Expression"
    role: 'ROLE_SYSTEM_ADMIN'
    value: ''
Phone.Display:
    type: 'twig'
    displayName: 'Phone Display Format'
    description: "A template to convert phone numbers into display version."
    role: 'ROLE_REGISTRAR'
    value: '{{ phone }}'
org.name:
    type: 'array'
    displayName: 'Organisation Name'
    description: "The name of your organisation"
    role: 'ROLE_REGISTRAR'
    value:
        long: Busybee Institute
        short: Bee
Org.Ext.Id:
    type: 'string'
    displayName: 'Organisation External Identifier'
    description: "The identifier given to your organisation by your parent or external education authority."
    role: 'ROLE_REGISTRAR'
Org.Postal.Address.1:
    type: 'text'
    displayName: 'Organisation Postal Address Line 1'
    description: "First line of this organisation's postal address."
    role: 'ROLE_REGISTRAR'
Org.Postal.Address.2:
    type: 'text'
    displayName: 'Organisation Postal Address Line 2'
    description: "Second line of this organisation's postal address."
    role: 'ROLE_REGISTRAR'
Org.Postal.Locality:
    type: 'text'
    displayName: 'Organisation Postal Locality'
    description: "Locality of this organisation's postal address. : Town, Suburb or Locality)"
    role: 'ROLE_REGISTRAR'
Org.Postal.Postcode: 
    type: 'string'
    displayName: 'Organisation Postal Post Code'
    description: "Post Code of this organisation's postal address."
    role: 'ROLE_REGISTRAR'
org.postal.territory: 
    type: 'enum'
    displayName: 'Organisation Postal Territory'
    description: "Territory of this organisation's postal address. : State, Province, County)"
    role: 'ROLE_REGISTRAR'
    choice: 'Address.TerritoryList'
Org.Contact.Name: 
    type: 'text'
    displayName: 'Organisation Contact'
    description: "The name of the person to contact in this organisation."
    role: 'ROLE_REGISTRAR'
Org.Contact.Phone: 
    type: 'string'
    displayName: 'Organisation Contact Phone Number'
    description: "The phone number of the person to contact in this organisation."
    role: 'ROLE_REGISTRAR'
    validator: 'phone.validator'
Org.Contact.Facsimile:
    type: 'string'
    displayName: 'Organisation Contact Facsimile Number'
    description: "The facsimile number of the person to contact in this organisation."
    role: 'ROLE_REGISTRAR'
    validator: 'phone.validator'
Org.Contact.Email:
    type: 'string'
    displayName: 'Organisation Contact Email Address'
    description: "The email address of the person to contact in this organisation."
    role: 'ROLE_REGISTRAR'
    validator: 'email.validator'
Org.Physical.Address.1:
    type: 'text'
    displayName: 'Organisation Physical Address Line 1'
    description: "First line of this organisation's physical address."
    role: 'ROLE_REGISTRAR'
Org.Physical.Address.2:
    type: 'text'
    displayName: 'Organisation Physical Address Line 2'
    description: "Second line of this organisation's physical address."
    role: 'ROLE_REGISTRAR'
Org.Physical.Locality:
    type: 'text'
    displayName: 'Organisation Physical Locality'
    description: "Locality of this organisation's physical address. : Town, Suburb or Locality)"
    role: 'ROLE_REGISTRAR'
Org.Physical.Postcode:
    type: 'string'
    displayName: 'Organisation Physical Post Code'
    description: "Post Code of this organisation's physical address."
    role: 'ROLE_REGISTRAR'
org.physical.territory:
    type: 'enum'
    displayName: 'Organisation Physical Territory'
    description: "Territory of this organisation's physical address. : State, Province, County)"
    role: 'ROLE_REGISTRAR'
    choice: 'Address.TerritoryList'
CountryType:
    type: 'text'
    displayName: 'Country Type Form Handler'
    description: "Determines how the country details are obtained and stored in the database."
    role: 'ROLE_SYSTEM_ADMIN'
    value: 'Symfony\Component\Form\Extension\Core\Type\CountryType'
firstDayofWeek:
    type: 'string'
    displayName: 'First Day of Week'
    description: 'The first day of the week for display purposes.  Monday or Sunday, defaults to Monday.'
    role: 'ROLE_REGISTRAR'
    value: 'Monday'
schoolweek:
    type: 'array'
    displayName: 'Days in the School Week'
    description: 'Defines the list of days that school would normally be open.'
    role: 'ROLE_ADMIN'
    value:
        'Monday':  'Mon'
        'Tuesday':  'Tue'
        'Wednesday':  'Wed'
        'Thursday':  'Thu'
        'Friday':  'Fri'
org.logo:
    type: 'image'
    displayName: 'Organisation Logo'
    description: 'The organisation Logo'
    role: 'ROLE_ADMIN'
    validator: 'App\Core\Validator\Logo'
    defaultValue: 'img/bee.png'
    value: null
org.transparent.logo:
    type: 'image'
    displayName: 'Organisation Transparent Logo'
    description: 'The organisation Logo in a transparent form.  Recommended to be 80% opacity. Only PNG or GIF image formats support transparency.'
    role: 'ROLE_ADMIN'
    validator: 'App\Core\Validator\Logo'
    defaultValue: 'img/bee-transparent.png'
    value: null
background.image:
    type: 'image'
    displayName: 'Background Image'
    description: 'Change the background displayed for the site.  The image needs to be a minimum of 1200px width.  You can load an image of 1M size, but the smaller the size the better.'
    role: 'ROLE_ADMIN'
    validator: 'App\Core\Validator\BackgroundImage'
    defaultValue: 'img/backgroundPage.jpg'
    value: null
SchoolDay.Open:
    type: 'time'
    displayName: 'School Day Open Time'
    description: 'At what time are students allowed on campus?'
    role: 'ROLE_ADMIN'
    value: '07:45'
SchoolDay.Begin:
    type: 'time'
    displayName: 'School Day Instruction Start Time'
    description: 'The time that teaching starts. Students would normally be considered late after this time.'
    role: 'ROLE_ADMIN'
    validator: null
    value: '08:45'
SchoolDay.Finish:
    type: 'time'
    displayName: 'School Day Instruction Finish Time'
    description: 'The time students are released for the day.'
    role: 'ROLE_ADMIN'
    validator: null
    value: '15:00'
SchoolDay.Close:
    type: 'time'
    displayName: 'School Day Close Time'
    description: 'The time the doors of the campus normally close, all after school and school activities finished.'
    role: 'ROLE_ADMIN'
    validator: null
    value: '17:00'
space.type:
    type: 'array'
    displayName: 'Type of Space'
    description: 'Spaces are spaces used with the Campus, such as classrooms, purpose built rooms and Storage Rooms.'
    role: 'ROLE_ADMIN'
    validator: null
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
            - staffroom
            - storage
    defaultValue: null
Staff.Categories:
    type: 'array'
    displayName: 'Staff Categories'
    description: 'List of the staff Categories.'
    role: 'ROLE_ADMIN'
    validator: null
    value:
        Not Specified: Unknown
        Teacher: Teacher
        Ancillary: Ancillary
        Cleaner: Cleaner
        Administrative: Administrative
phone.country.code:
    type: 'enum'
    displayName: 'Phone Country Code'
    description: 'Default phone country code.'
    role: 'ROLE_SYSTEM_ADMIN'
    choice: 'phone.country.list'
    validator: null
Person.Import:
    type: 'array'
    displayName: 'Person Import Defaults'
    description: 'Default values added to imported records.'
    role: 'ROLE_REGISTRAR'
    validator: null
Phone.Format:
    type: 'twig'
    displayName: 'Phone Full Display Format'
    description: "A template to convert phone numbers into full display version."
    role: 'ROLE_REGISTRAR'
    value: '{{ phoneNumber }}'
Student.CareGiver.Relationship.List:
    type: 'array'
    displayName: 'List of Student - Care Giver Relationship'
    description: 'List of Student - Care Giver Relationship'
    role: 'ROLE_ADMIN'
    value:
        Not Defined: Unknown
        Parent: Parent
        Guardian: Guardian
        Grand Parent: Grand Parent
        Family Friend: Family Friend
Ethnicity.List:
    type: 'array'
    displayName: 'List of Ethnicities'
    description: 'List of Ethnicities.  Uses the Australian Standard to create this list'
    role: 'ROLE_ADMIN'
    value:
        Inadequately described : '0'
        Not stated : '1'
        Eurasian: '901'
        Asian: '902'
        African, so decribed: '903'
        European, so decribed: '904'
        Caucasian, so decribed: '905'
        Creole, so decribed: '906'
        Oceanian: '1000'
        Australian Peoples: '1100'
        New Zealand Peoples: '1200'
        Melanesian and Papuan: '1300'
        Micronesian: '1400'
        Polynesian: '1500'
        North-West European: '2000'
        British: '2100'
        Western European: '2300'
        Northern European: '2400'
        Southern and  Eastern European: '3000'
        Southern European: '3100'
        South Eastern European: '3200'
        Eastern European: '3300'
        North African and Middle Eastern: '4000'
        Arab: '4100'
        Peoples of the Sudan: '4300'
        Other North African and Middle Eastern: '4900'
        South-East  Asian: '5000'
        Mainland South-East Asian: '5100'
        Maritime South-East Asian: '5200'
        North-East Asian: '6000'
        Chinese Asian: '6100'
        Other North-East Asian: '6900'
        Southern and Central Asian: '7000'
        Southern Asian: '7100'
        Central Asian: '7200'
        Peoples of the Americas: '8000'
        North American: '8100'
        South American: '8200'
        Central American: '8300'
        Caribbean Islander: '8400'
        Sub-Saharan African: '9000'
        Central and West African: '9100'
        Southern and East African: '9200'
religion.list:
    type: 'array'
    displayName: 'List of Religions'
    description: 'List of Religions.  Uses the Australian Standard to create this list'
    role: 'ROLE_ADMIN'
    value:
        Aboriginal Evangelical Missions: 2801
        Acts 2 Alliance: 2416
        Agnosticism: 7201
        Albanian Orthodox: 2231
        Ancestor Veneration: 6051
        Ancient Church of the East: 2222
        Anglican Catholic Church: 2013
        Anglican Church of Australia: 2012
        Animism: 6131
        Antiochian Orthodox: 2232
        Apostolic Church (Australia): 2401
        Apostolic Church of Queensland: 2901
        Armenian Apostolic: 2212
        Assyrian Apostolic, nec: 2229
        Assyrian Church of the East: 2221
        Atheism: 7202
        Australian Aboriginal Traditional Religions: 6011
        Australian Christian Churches (Assemblies of God): 2402
        Baha'i: 6031
        Baptist: 2031
        Bethesda Ministries International (Bethesda Churches): 2403
        Born Again Christian : 2802
        Brethren: 2051
        Buddhism: 1011
        C3 Global (Christian City Church): 2404
        Caodaism: 6991
        Catholic, nec: 2079
        Chaldean Catholic: 2075
        Chinese Religions, nec: 6059
        Christadelphians: 2902
        Christian and Missionary Alliance: 2803
        Christian Church in Australia: 2417
        Christian Community Churches of Australia: 2811
        Christian Science: 2903
        Church of Christ (Nondenominational): 2112
        Church of Jesus Christ of Latter-day Saints: 2151
        Church of Scientology : 6992
        Church of the Nazarene : 2804
        Churches of Christ (Conference): 2111
        Community of Christ: 2152
        Confucianism : 6052
        Congregational: 2805
        Coptic Orthodox: 2214
        CRC International (Christian Revival Crusade): 2407
        Druidism: 6132
        Druse: 6071
        Eastern Orthodox, nec: 2239
        Eckankar: 6993
        Ethiopian Orthodox: 2216
        Ethnic Evangelical Churches: 2806
        Foursquare Gospel Church: 2411
        Free Reformed: 2253
        Full Gospel Church of Australia (Full Gospel Church): 2412
        Gnostic Christians: 2904
        Grace Communion International (Worldwide Church of God): 2915
        Greek Orthodox: 2233
        Hinduism: 3011
        Humanism: 7203
        Independent Evangelical Churches: 2807
        International Church of Christ: 2113
        International Network of Churches (Christian Outreach Centres): 2406
        Islam: 4011
        Jainism: 6997
        Japanese Religions, nec: 6119
        Jehovah's Witnesses: 2131
        Judaism: 5011
        Liberal Catholic Church: 2905
        Lutheran: 2171
        Macedonian Orthodox : 2234
        Mandaean: 6901
        Maronite Catholic: 2072
        Melkite Catholic: 2073
        Methodist, so described: 2812
        Multi Faith: 7301
        Nature Religions, nec: 6139
        New Age: 7302
        New Apostolic Church : 2906
        New Churches (Swedenborgian): 2907
        No Religion, so described: 7101
        Oriental Orthodox, nec: 2219
        Other Anglican: 2019
        Other Christian, nec: 2999
        Other Protestant, nec: 2899
        Other Spiritual Beliefs, nec: 7399
        Own Spiritual Beliefs: 7303
        Paganism: 6133
        Pentecostal City Life Church: 2418
        Pentecostal, nec: 2499
        Presbyterian: 2251
        Rastafari: 6994
        Ratana (Maori): 2908
        Rationalism: 7204
        Reformed: 2252
        Religious Groups, nec : 6999
        Religious Science: 2911
        Religious Society of Friends (Quakers) : 2912
        Revival Centres: 2413
        Revival Fellowship: 2421
        Rhema Family Church: 2414
        Romanian Orthodox: 2235
        Russian Orthodox: 2236
        Salvation Army: 2271
        Satanism : 6995
        Secular Beliefs nec: 7299
        Serbian Orthodox: 2237
        Seventh-day Adventist : 2311
        Shinto: 6111
        Sikhism: 6151
        Spiritualism: 6171
        Sukyo Mahikari: 6112
        Syrian Orthodox: 2215
        Syro Malabar Catholic: 2076
        Taoism: 6053
        Temple Society: 2913
        Tenrikyo: 6113
        Theism: 7304
        Theosophy: 6996
        Ukrainian Catholic: 2074
        Ukrainian Orthodox: 2238
        United Methodist Church: 2813
        United Pentecostal: 2415
        Uniting Church: 2331
        Universal Unitarianism: 7305
        Victory Life Centre: 2422
        Victory Worship Centre: 2423
        Wesleyan Methodist Church: 2808
        Western Catholic: 2071
        Wiccan (Witchcraft): 6135
        Worship Centre network: 2424
        Yezidi: 6902
        Zoroastrianism: 6998
Residency.List:
    type: 'array'
    displayName: 'List of Residency Status'
    description: 'List of Residency Status.  Usually defined by the government.'
    role: 'ROLE_ADMIN'
    value:
        Citizen: Citizen
        Temporary: Temporary
        Permanent: Permanent
        Visitor: Visitor
Settings.Default.Overwrite:
    type: 'system'
    displayName: 'Setting File Overwrite'
    description: 'A file name that allows the update process to change the default settings to match the users pre-set details.'
    role: 'ROLE_SYSTEM_ADMIN'
    value: ''
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