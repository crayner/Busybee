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
    value: 0.0.02
Address.Format:
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
Person.GenderList:
    type: 'array'
    displayName: 'Gender List'
    description: 'A list of genders used in the system.'
    role: 'ROLE_REGISTRAR'
    value:
        'Unspecified': 'U'
        'Male': 'M'
        'Female': 'F'
        'Other': '0'
Person.TitleList:
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
Phone.TypeList:
    type: 'array'
    displayName: 'Types of Phones'
    description: "List of phone types. The key : key: value) is displayed on your system, and the value is stored in the database."
    role: 'ROLE_REGISTRAR'
    value:
        'Home': 'Home'
        'Mobile': 'Mobile'
        'Work': 'Work'
        'Imported': 'Imported'
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
Org.Postal.Territory: 
    type: 'string'
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
    type: 'string'
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
    validator: 'busybee_core_template.validator.logo'
    value: 'img/bee.png'
org.logo.transparent:
    type: 'image'
    displayName: 'Organisation Transparent Logo'
    description: 'The organisation Logo in a transparent form.  Recommended to be 80% opacity. Only PNG or GIF image formats support transparency.'
    role: 'ROLE_ADMIN'
    validator: 'busybee_core_template.validator.logo'
    value: 'img/bee-transparent.png'
background.image:
    type: 'image'
    displayName: 'Background Image'
    description: 'Change the background displayed for the site.  The image needs to be a minimum of 1200px width.  You can load an image of 1M size, but the smaller the size the better.'
    role: 'ROLE_ADMIN'
    validator: 'App\Core\Validator\BackgroundImage'
    value: 'img/backgroundPage.jpg'
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
Campus.Space.Type:
    type: 'array'
    displayName: 'Type of Space'
    description: 'Spaces are spaces used with the Campus, such as classrooms, purpose built rooms and Storage Rooms.'
    role: 'ROLE_ADMIN'
    validator: null
    value:
        Classroom: Classroom
        Hall: Hall
        Laboratory: Laboratory
        Library: Library
        Office: Office
        Outdoor: Outdoor
        Meeting Room: Meeting Room
        Performance: Performance
        Staffroom: Staffroom
        Storage: Storage
        Study: Study
        Undercover: Undercover
        Other: Other
    choice: Classroom
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
Religion.List:
    type: 'array'
    displayName: 'List of Religions'
    description: 'List of Religions.  Uses the Australian Standard to create this list'
    role: 'ROLE_ADMIN'
    value:
        A God: 7304
        A2A Churches: 2416
        A2A Community Church: 2416
        A2A Pentecostal: 2416
        Aboriginal Christian Fellowship: 2801
        Aboriginal Church: 2801
        Aboriginal Community Church: 2801
        Aboriginal Dreamtime: 6011
        Aboriginal Evangelical: 2801
        Aboriginal Evangelical Church: 2801
        Aboriginal Evangelical Fellowship: 2801
        Aboriginal Evangelical Missions: 2801
        Aboriginal Fellowship: 2801
        Aboriginal Inland Mission: 2801
        Aboriginal Lore: 6011
        Aboriginal Mission: 2801
        Aboriginal Religion: 6011
        Aboriginal Spirituality: 6011
        Aboriginal United Missions: 2801
        Aborignal Pentecostal: 2400
        ACC AOG : 2402
        ACC AOG Christian Life Centre: 2402
        ACC AOG Pentecostal: 2402
        ACC Calvary Church: 2402
        ACC Chinese Community Church: 2402
        ACC Influencer Church: 2402
        ACC Pentecostal: 2402
        ACC Revival Life Centre: 2402
        ACC Victory Church: 2402
        Acts 2 Alliance: 2416
        Acts 2 Alliance Pentecostal: 2416
        Adelaide Revival: 2413
        Adelaide Revival Fellowship: 2421
        Advaita: 3011
        Adventist Christian: 2311
        Adventist Reform Seventh Day: 2311
        Aetherius Society: 6999
        African Episcopal Methodist Church: 2812
        African Methodist Episcopal Zion Church: 2812
        Afrikaans Christian Church of Australia: 2252
        Ageless Wisdom: 6996
        Aglipayan: 2012
        Agnost: 7201
        Agnostic: 7201
        Agnosticism: 7201
        Ahmadi: 4011
        Ahmadiyya : 4011
        Aim Aboriginal Inland Mission: 2801
        Aim Australian Indigenous Ministry: 2801
        Ainnu: 6119
        Alavi: 4011
        Alawi: 4011
        Alawite: 4011
        Albanian Orthodox : 2231
        Alevi: 4011
        Alevibektasi: 4011
        Alevism: 4011
        Alexandrian: 6135
        Alien: 0000
        All Christian: 2000
        All Denominations: 2000
        All Embracing: 7301
        All Faiths: 7301
        All Nation Christian Fellowship: 2499
        All Religions: 7301
        Altruism: 7299
        Altruist: 7299
        American Methodist: 2812
        American Native Church: 6139
        American Orthodox: 2239
        Amish: 2999
        Amun Ra: 6139
        Anabaptist: 2999
        Ananda Marga: 3011
        Ancestor Veneration: 6051
        Ancestor Worship: 6051
        Ancient Church of the East: 2222
        Angels: 7399
        Anglican: 2012
        Anglican Catholic: 2013
        Anglican Catholic Church: 2013
        Anglican Catholic Church in Australia: 2013
        Anglican Church: 2012
        Anglican Church of Australia: 2012
        Anglican Church of Torres Strait: 2013
        Anglican Deaf Ministry: 2012
        Anglican Independent Communion: 2019
        Anglican Mainstream: 2019
        Anglican Muslim: 7301
        Anglican Realignment: 2019
        Anglo Catholic: 2012
        Animism: 6131
        Anthroposophist: 6996
        Anthroposophy: 6996
        Anti Christ: 6995
        Antioch Orthodox: 2232
        Antiochian Orthodox: 2232
        ANZAC: 7399
        AOG  Christian: 2402
        AOG  Christian Churches: 2402
        AOG  Church: 2402
        AOG  Pentecostal: 2402
        Apostolic Armenian: 2212
        Apostolic Assyrian Church: 2221
        Apostolic Assyrian Church of the East: 2221
        Apostolic Christian: 2000
        Apostolic Christian Centre: 2401
        Apostolic Christian Church of Nazarene: 2804
        Apostolic Church (Australia): 2401
        Apostolic Church of Australia: 2401
        Apostolic Church of Queensland: 2901
        Apostolic Faith Mission of Australia: 2499
        Apostolic Pentecostal Family Church: 2401
        Apostolic Pentecostal: 2401
        Apostolic Society: 2999
        Arabic Orthodox: 2210
        Armenian Apostolic: 2212
        Armenian Apostolic Christian: 2212
        Armenian Apostolic Church: 2212
        Armenian Apostolic Church Gregorian: 2212
        Armenian Apostolic Orthodox: 2212
        Armenian Catholic: 2079
        Armenian Evangelical Church: 2806
        Armenian Gregorian: 2212
        Asatru: 6139
        Asatru Heathen: 6139
        Assemblies of God: 2402
        Assemblies of God Christian: 2402
        Assemblies of God in Australia: 2402
        Assembly of Christian Brethren: 2811
        Assembly of God Pentecostal: 2402
        Associated Churches of Christ: 2112
        Assyrian Apostolic : 2220
        Assyrian Apostolic, nec: 2229
        Assyrian Christianity: 2220
        Assyrian Church of the East: 2221
        Assyrian Orthodox Church of the East: 2221
        Astrology: 7399
        Atheism: 7202
        Atheist: 7202
        Austral Asian Christian Church: 2806
        Australian Aboriginal Evangelical Mission: 2801
        Australian Aboriginal Ministry: 2801
        Australian Aboriginal Traditional Religions: 6011
        Australian and New Zealand Union For Progressive Judaism: 5011
        Australian Anglican: 2012
        Australian Catholic: 2071
        Australian Christian: 2000
        Australian Christian Churches: 2402
        Australian Christian Churches (Assemblies of God): 2402
        Australian Christian Churches AOG : 2402
        Australian Christian Churches Pentecostal: 2402
        Australian Church of Antioch: 6996
        Australian Church of Christ: 2111
        Australian Deaf Christians: 2999
        Australian Evangelical Lutheran Church: 2171
        Australian Indigenous Ministries: 2801
        Australian Inland Mission: 2801
        Australian Reformed Church: 2252
        Australian Revival Fellowship: 2421
        Australian Unions For Progressive Judaism: 5011
        Australian Wesleyan Church: 2808
        "Baha'i": 6031
        "Baha'i Faith": 6031
        "Baha'i Word Faith": 6031
        Balinese Hindu: 3011
        Baptist: 2031
        Baptist Deaf Ministry: 2031
        Baptist Faith: 2031
        Baptist Independent: 2031
        Basel Mission: 2899
        Basilio Scientific School: 6996
        Belarus Orthodox: 2239
        Belief: 7300
        Believe in God and Jesus: 2000
        Believer in God: 7304
        Believer in Jesus Christ: 2000
        Belong To No Sect: 7101
        Berean Bible: 2999
        Berean Bible Church: 2899
        Bethany Christian Fellowship: 2899
        Bethel Christian Centre: 2807
        Bethesda: 2403
        Bethesda Churches: 2403
        Bethesda Ministries International (Bethesda Churches): 2403
        Bethesda Pentecostal: 2403
        Bhakti Yoga: 3011
        Bible Based Christian: 2000
        Bible Believing Christian: 2000
        Bible Methodist: 2812
        Bible Ministries Church: 2899
        Bible Presbyterian: 2251
        Bible Salvation Assembly: 2899
        Bible Salvation Fellowship: 2999
        Black Magic: 6995
        Body Felt Salvation Church: 2499
        Bohemian Brethren: 2899
        Born Again: 2802
        Born Again  Christian: 2802
        Born Again Believer: 2802
        Brahma Kumari: 3011
        Brahmin: 3011
        Branch Davidians: 2311
        Bread of Life Christian Church: 2999
        Brethren: 2051
        Buddhism: 1011
        Buddhist: 1011
        Buddhist Christian: 7301
        Buddhist Zen: 1011
        Buddism Hinduism: 7301
        Bulgarian Eastern Orthodox: 2238
        Bulgarian Orthodox: 2239
        Byelorussian Autocephalic Orthodox: 2238
        Byzantine Catholic: 2079
        C of E: 2012
        C3: 2404
        C3 Church Global: 2404
        C3 Global (Christian City Church): 2404
        C3 Pentecostal: 2404
        Calvary Chapel: 2000
        Calvary Chapel Movement: 2899
        Calvinist: 2251
        Calvinist Protestant: 2251
        Cambodian Theravada: 1011
        Cao Dais: 6991
        Caodaism: 6991
        Capitalism: 0000
        Catch the Fire: 2499
        Catholic: 2071
        Catholic and Buddhism: 7301
        Catholic Apostolic Church: 2999
        Catholic Chaldean: 2075
        Catholic Christian: 2071
        Catholic Deaf Ministry: 2071
        Catholic Greek: 2073
        Catholic Malankara: 2076
        Catholic of the East: 2999
        Catholic Orthodox: 6996
        Catholic, nec: 2079
        Caulfield Evangelical Methodist : 2807
        CCCA  Assembly: 2811
        CCCA Family Fellowship: 2811
        CCCA Gospel Fellowship: 2811
        CCCA Gospel Hall: 2811
        Celtic Christian: 2999
        Celtic Druidism: 6132
        Celtic Faith: 6139
        Central Evangelical Church: 2899
        Chabad: 5011
        Chaldean Catholic: 2075
        Chan Buddhism: 1011
        Chapel: 2000
        Charedi : 5011
        Chassidic Movement: 5011
        Child of God: 2000
        Child of the Earth: 6139
        Child of the Universe: 0000
        China Mission: 2899
        Chinese Alliance Church: 2806
        Chinese Christian Church: 2806
        Chinese Church: 2000
        Chinese Congregation Church: 2806
        Chinese Evangelical Church: 2806
        Chinese Methodist Church: 2806
        Chinese Methodist Church in Australia: 2806
        Chinese Religions: 6050
        Chinese Religions, nec: 6059
        Christ: 2000
        Christ Church of England: 2012
        Christ Consciousness Yhwh: 6996
        Christadelphian: 2902
        Christadelphian Christian: 2902
        Christian: 2000
        Christian - Assyrian: 2220
        Christian - Congregational Church: 2805
        Christian - Evangelical Church of Australia: 2807
        Christian  Jacobite: 2215
        Christian - No Specific Denomination: 2000
        Christian Aboriginal Dreaming: 7301
        Christian Alliance Church: 2803
        Christian and Missionary Alliance: 2803
        Christian Anglican: 2012
        Christian Assembly of God: 2402
        Christian Assyrian Orthodox Church: 2220
        Christian Buddhist: 7301
        Christian Catholic: 2071
        Christian Church: 2000
        Christian Church Congregational: 2805
        Christian Church in Australia: 2417
        Christian Church of Christ: 2111
        Christian Church of the East: 2222
        Christian Church of Tonga: 2806
        Christian Churches of Australia Pentecostal: 2417
        Christian City Church: 2404
        Christian City Church International: 2404
        Christian City Churches International: 2404
        Christian Community Churches of Australia: 2811
        Christian Community Movement For Renewal: 6996
        Christian Coptic Orthodox: 2214
        Christian CRC: 2407
        Christian Crusade: 2407
        Christian Destiny Church: 2999
        Christian Disciples: 2899
        Christian Faith: 2000
        Christian Fellowship: 2000
        Christian Four Square Church: 2411
        Christian Foursquare: 2411
        Christian Free Church: 2807
        Christian Free Church of Tonga: 2806
        Christian Free Reformed: 2253
        Christian Full Gospel: 2412
        Christian Gregorian: 2212
        Christian Gregorian Church: 2212
        Christian Home Church: 2999
        Christian Home Group: 2999
        Christian House Church: 2999
        Christian Israelite Church: 2999
        Christian Jesus Is Lord: 2999
        Christian Latter Day Saints: 2151
        Christian Life Centre: 2416
        Christian Marthomite: 2215
        Christian Mission Fellowship: 2899
        Christian Molokan: 2999
        Christian Gnostic: 2904
        Christian Orthodox Macedonian: 2234
        Christian Outreach Centre: 2406
        Christian Outreach Pentecostal: 2406
        Christian Peoples Church: 2800
        Christian Plymouth Brethren: 2051
        Christian Quaker: 2912
        Christian Ratana: 2908
        Christian Reformed Church: 2252
        Christian Reformed Church of Australia: 2252
        Christian Reformed Churches of Australia: 2252
        Christian Reformed Community Church: 2252
        Christian Reformed Fellowship: 2252
        Christian Renewal: 2999
        Christian Revival Centre: 2413
        Christian Revival Crusade: 2407
        Christian Revival Fellowship: 2421
        Christian Revival Pentecostal: 2413
        Christian Rhema: 2414
        Christian Russian Orthodox: 2236
        Christian Science: 2903
        Christian Scientist: 2903
        Christian Seventh Day Adventist: 2311
        Christian Shire City Church: 2404
        Christian Spiritual Church: 2999
        Christian Tokaikolo: 2806
        Christian Unaffiliated: 2000
        Christian Vineyard: 2000
        Christian Wesleyan Methodist Church: 2808
        Christianity: 2000
        Christianity Samoan Methodist Church: 2806
        Church Missionary Society: 2012
        Church of Australia: 2000
        Church of Canada: 2000
        Church of Christ: 2111
        Church of Christ (Non-denominational): 2112
        Church of Christ Iglesia ni Cristo: 2806
        Church of Christ Restored: 2999
        Church of Christ Scientist: 2903
        Church of Christianity: 2000
        Church of Denmark: 2171
        Church of Divine Unity: 2999
        Church of England: 2012
        Church of God: 2000
        Church of India: 2012
        Church of Ireland: 2012
        Church of Jesus Christ: 2000
        Church of Jesus Christ of Latter Day Saints: 2151
        Church of Jesus Christ of Latter Day Saints Mormons: 2151
        Church of New Jerusalem: 2907
        Church of Norway: 2171
        Church of Perfect Liberty: 6999
        Church of Satan: 6995
        Church of Scientology: 6992
        Church of Scotland: 2251
        Church of South Africa: 2012
        Church of South India: 2012
        Church of Sweden: 2171
        Church of the East: 2220
        Church of the Nazarene: 2804
        Church of the Nazarene Evangelical: 2804
        Church of Things: 7299
        Church of Tonga Christian: 2806
        Church of Torres Strait: 2013
        Church of TSI: 2013
        Church of Wales: 2012
        Church of Wicca: 6135
        Church Origin: 2999
        Church Universal and Triumphant: 6996
        Churches of Christ: 2110
        Churches of Christ (Conference): 2111
        City Life Pentecostal: 2418
        Combination of Faiths: 7301
        Combination of Religions: 7301
        Community Churches of Australia: 2811
        Community of Christ: 2152
        Compassionism: 7399
        Confraternity of the Blessed Sacrament of the Body and Blood of Christ: 2019
        Confucianism: 6052
        Confucius: 6052
        Congregation of Friends: 2912
        Congregational: 2805
        Congregational Christian Church: 2805
        Congregational Christian Church of Samoa in Australia: 2805
        Congregationalist: 2805
        Congregationalist Christian Church: 2805
        Conservadox: 5011
        Constitutional Church of Tonga: 2806
        Continuing Anglican Movement: 2019
        Cook Is Christian Church: 2806
        Cook Island Christian Church: 2806
        Cook Island Congregational: 2806
        Cook Island Presbyterian: 2251
        Cook Islands Christian Church: 2806
        Coptic Catholic: 2079
        Coptic Christian: 2214
        Coptic Egyptian: 2214
        Coptic Orthodox: 2214
        Coptic Orthodox Christian: 2214
        Coptic Orthodox Church: 2214
        CRC  International Revival: 2407
        CRC  Pentecostal: 2407
        CRC Churches International: 2407
        CRC International (Christian Revival Crusade): 2407
        Creation Spirituality: 7399
        Crosslink Christian Network: 2499
        Crosslink Pentecostal: 2499
        Crossroads Christian Church: 2807
        Cypriot Orthodox: 2233
        Danish Lutheran Church: 2171
        Danish Protestant: 2171
        Daoist: 6053
        Deaf Christian Fellowship: 2999
        Deaf Community Church: 2999
        Dedication: 7300
        Deep Spiritual Lifestyle: 7303
        Dervish: 4011
        Devi Worship: 3011
        Devil Worshipper: 6995
        Diamond Way: 1011
        Disciple of Sri Chinmoy: 3011
        Discordianism: 6999
        Do Not Believe: 7101
        Don't Believe in God: 7202
        Don't Have One: 7101
        Dreamtime: 6011
        Druid: 6132
        Druidism: 6132
        Druids: 6132
        Druse: 6071
        Druze: 6071
        Dutch Free Reformed: 2252
        Dutch Hervormd: 2252
        Dutch Reformed Church: 2252
        Dutch Remonstrant: 2805
        Earth Church Australia: 6139
        Earth Religion: 6130
        Earth Spiritualism: 6171
        Eastern Orthodox : 2230
        Eastern Orthodox Christian: 2230
        Eastern Orthodox Macedonian: 2234
        Eastern Orthodox, nec: 2239
        Eastern Rites Catholic: 2079
        Eastern Spirituality: 7399
        Ecclesia of God: 2999
        Eckankar: 6993
        Eclectic Beliefs: 7301
        Ecumenical Christian: 2000
        EFCA: 2806
        EFKS Congregational Christian Church of Samoa: 2805
        Egocentrism: 0000
        Egyptian Coptic Orthodox: 2214
        Egyptian Orthodox: 2214
        Egyptian Orthodox Coptic: 2214
        Elementalism: 6139
        Elim Pentecostal: 2499
        Emmanuel Pentecostal: 2499
        English Methodist: 2812
        Environmental Spiritualism: 7399
        Episcopal Church: 2012
        Episcopal Church of Scotland: 2012
        Episcopal Methodist: 2812
        Episcopalian: 2012
        Eritrean Orthodox: 2219
        Eritrean Tewahedo Orthodox: 2219
        Eshamism: 6139
        Esoteric Christian: 6996
        Esoteric Spiritual: 7399
        Espiritism: 6171
        Espiritist: 6171
        Estonian Orthodox: 2239
        Ethical Green Pagan: 7399
        Ethiopian Orthodox: 2216
        Ethiopian Orthodox Christian: 2216
        Ethiopian Orthodox Church: 2216
        Ethiopian Orthodox Tewahedo: 2216
        Ethiopian Tewahedo Orthodox: 2216
        Ethnic Evangelical Churches: 2806
        Evangelical  Christian: 2800
        Evangelical Anglican: 2012
        Evangelical Baptist: 2031
        Evangelical Chinese Church: 2806
        Evangelical Christian Independent: 2800
        Evangelical Christianity Methodist: 2812
        Evangelical Church of Czech Brethren: 2899
        Evangelical Church of Papua: 2806
        Evangelical Formosan Church: 2806
        Evangelical Free Church in Australia: 2807
        Evangelical Greek Free Church: 2806
        Evangelical Independent: 2807
        Evangelical Lutheran: 2171
        Evangelical Mission: 2899
        Evangelical Nondenominational: 2800
        Evangelical Presbyterian: 2251
        Evangelical Protestant: 2800
        Evangelical Reformed Church: 2252
        Evangelical Spiritual Brethren: 2811
        Evangelist Fellowship: 2807
        Everlasting Gospel: 2899
        Evolutionism: 7299
        Exclusive Brethren: 2051
        Existentialism: 7299
        Faith: 7300
        Faith Christian Church: 2000
        Faith Christian Church  Pentecostal: 2499
        Faith in God: 7304
        Faith Life Pentecostal: 2418
        Falun Dafa: 7399
        Falun Gong: 7399
        Family Church: 2000
        Family of God Church: 2999
        Family of God Fellowship: 2999
        Family of Love: 2999
        Fellowship of Congregational Churches: 2805
        Fellowship of Evangelical Churches of Australia: 2807
        Fellowship of Independent Evangelical Churches: 2807
        Feminist Spirituality: 7399
        Filipine Independent: 2012
        Finnish Lutheran: 2171
        Finnish Orthodox: 2239
        Finnish Pentecostal Church: 2499
        First Church of Christ Scientist: 2903
        Follower of the Way: 6059
        Follower of Yahshua: 2999
        Football: 0000
        Forward in Faith Ministries International: 2499
        Forward in Faith Ministries Pentecostal: 2499
        Four Square Calvary Chapel: 2411
        Four Square Gospel: 2411
        Four Square Pentecostal: 2411
        Foursquare Gospel Church: 2411
        Free Christian Church: 2807
        Free Church of Scotland: 2251
        Free Church of Tonga: 2806
        Free Deist: 3011
        Free Evangelical Church of Australia: 2807
        Free Presbyterian: 2251
        Free Presbyterian Church of Scotland: 2251
        Free Reformed: 2253
        Free Reformed Church of Australia: 2253
        Free Thought: 7299
        Free Wesleyan Church of Tonga: 2806
        Free Wesleyan Methodist: 2808
        Full Gospel Christian: 2412
        Full Gospel Church Australia: 2412
        Full Gospel Church of Australia (Full Gospel Church): 2412
        Full Gospel Churches of Australia: 2412
        Full Gospel Fellowship: 2412
        Full Gospel Pentecostal: 2412
        Fundamentalist Christian: 2800
        Gaia: 6139
        Gaian: 6139
        Gandhian: 3011
        Gardnerian: 6135
        Gaudiga Vaisnava: 3011
        General Beliefs: 7300
        Georgian Orthodox: 2236
        Gereformeerde Evangeliese Kerk: 2252
        German Evangelical Lutheran: 2171
        German Lutheran: 2171
        Ghana Methodist Church: 2812
        Glory of the Cross Ministry: 2499
        Gnostic Christian: 2904
        God Loving: 7304
        God Squad: 2800
        Goddess Spirituality: 6139
        Goddess Worshiper: 6139
        Good Samaritan Church of Truth: 2999
        Gospel Christian Church: 2412
        Gospel Community: 2899
        Gospel of Jesus Christ: 2000
        Grace Bible Churches: 2807
        Grace Christian: 2000
        Grace Communion International: 2915
        Grace Communion International (Worldwide Church of God): 2915
        Grace Community Church: 2807
        Grail Movement: 6999
        Great Commission Church: 2899
        Great White Spirit: 6996
        Greek Evangelical: 2806
        Greek Free Churches: 2806
        Greek Gods: 6999
        Greek Orthodox: 2233
        Green Pagan: 7399
        Gregorian Orthodox: 2212
        Gurdjieff: 6999
        Haahi Ratana: 2908
        Hallelujah Worship Centre: 2899
        Happiness: 0000
        Hare Krishna: 3011
        Haredi : 5011
        Hau Hau Paimarire: 2999
        Heathen: 0000
        Heathen Asatru: 6139
        Heaven: 7399
        Hedonist: 6999
        Herbalist: 6139
        High Anglican: 2012
        High Church of England: 2012
        Hillsong: 2402
        Hillsong Assemblies of God: 2402
        Hillsong Church: 2402
        Hindu: 3011
        Hindu Brahmin: 3011
        Hindu Dharma: 3011
        Hindu Indian: 3011
        Hindu Lingayat: 3011
        Hindu Punjabi: 3011
        Hindu Swarminarayan: 3011
        Hinduism: 3011
        Hippy: 0000
        Hoa Hao Buddhism: 1011
        Holiness Church: 2899
        Holistic: 7302
        Holistic Spiritual: 7303
        Holy Apostolic Catholic Church: 2999
        Holy Bible: 2000
        House Apostolic Church: 2401
        House Christian Church: 2000
        House Church Revival Ministries: 2421
        Huguenot: 2252
        Humanism: 7203
        Humanist: 7203
        Hungarian Reformed Church: 2252
        Hussite: 2899
        Iglesia Filipina Independiente: 2012
        Iglesia Ni Cristo: 2806
        Inadequately Described: 0000
        Independent Evangelical Christian: 2800
        Independent Evangelical Churches: 2807
        Independent  Christian: 2000
        Independent Anglican Church of Canada: 2019
        Independent Bible Believing Christian: 2800
        Independent Bible Church: 2807
        Independent Christian Household Church: 2800
        Independent Christian Reformed: 2253
        Independent Church of Australia: 6996
        Independent Reformed: 2252
        Indian Orthodox Christian: 2215
        Individual Faith: 7303
        Indonesian Christian Church: 2806
        Indonesian Church: 2806
        Infinite Way Study Centres: 2911
        Influencer ACC: 2402
        Influencer Church: 2402
        Influencer Pentecostal: 2402
        Inner Life Ministries: 2999
        Inner Peace Movement: 6999
        Inner Religiousness: 7303
        International Christian Outreach: 2406
        International Church of Christ: 2113
        International Network Christian Outreach: 2406
        International Network of Churches: 2406
        International Network of Churches (Christian Outreach Centres): 2406
        Irish Catholic: 2071
        Islam: 4011
        Islam Ahmadiyya: 4011
        Islam Shia: 4011
        Islamic: 4011
        Israelite: 5011
        Jacobite: 2215
        Jacobite Syrian Orthodox: 2215
        Jain: 6997
        Jainism: 6997
        Japanese Religions: 6110
        Japanese Religions, nec: 6119
        Jatt Sikh: 6151
        Jehovah Witness: 2131
        Jehovah Witness Christian: 2131
        Jehovah's Witnesses: 2131
        Jesus Christ Church of Latter Day Saints: 2151
        Jesus Christ Church of Latter Day Saints Mormons: 2151
        Jesus Christ Follower: 2000
        Jew: 5011
        Jewish: 5011
        Jewish Conservative: 5011
        Jewish Liberal: 5011
        Jewish Modern Orthodox: 5011
        Jewish Orthodox: 5011
        Jewish Reform: 5011
        Jodoshu: 1011
        John Wesley Methodist: 2808
        Johrei Association: 6119
        Joss: 6053
        Judaic Christian: 2999
        Judaism: 5011
        Judaism Jewish: 5011
        Judaism Progressive: 5011
        Judeo Christian: 2999
        Kardecist: 6171
        Karma: 7399
        Karman Church: 2999
        Kashmir Shaivism: 3011
        Katolic: 2071
        Kerala Catholic: 2076
        Kiranti: 6999
        Kirat: 6999
        Kirat Mum Dhum: 6999
        Knights Templar: 2999
        Krishna: 3011
        Krishna Consciousness: 3011
        Lama: 1011
        Lamaism: 1011
        Latin Mass : 2079
        Latter Day Saints : 2150
        Latvian Lutheran: 2171
        Latvian Orthodox: 2239
        Lebanese Orthodox Antiochian: 2232
        Liberal Catholic Church: 2905
        Liberal Humanitarian: 7000
        Liberal Jew: 5011
        Liberal Jewish: 5011
        Liberal Judaism: 5011
        Lingsu: 6059
        Living Word Fellowship: 2999
        London Mission Society: 2805
        Lubavich: 5011
        Lubavitch: 5011
        Lutheran: 2171
        Lutheran Church: 2171
        Lutheran Deaf Ministry: 2171
        Macedonian Christian Orthodox: 2234
        Macedonian Orthodox: 2234
        Macedonian Orthodox Christian: 2234
        Macedonian Orthodox Church: 2234
        Mahikari: 6112
        Makedonian Orthodox: 2234
        Malankara Syrian Orthodox: 2215
        Malankara Syrian Orthodox Indian: 2215
        Mandaean: 6901
        Mandaean Nazoraean: 6901
        Mandian: 6901
        Manna Christian Fellowship: 2899
        Maori Ringatu: 6999
        Mar Thoma Syrian Orthodox: 2215
        Margaret Court Ministries: 2422
        Maronite: 2072
        Maronite Catholic: 2072
        Marthoma Christian: 2215
        Marthoma Syrian Christian: 2215
        Marxism: 0000
        Masorti : 5011
        Meditation: 7399
        Melkite Catholic: 2073
        Mennonite: 2999
        Messianic Christian: 2999
        Metaphysicist: 7399
        Metaphysics: 7399
        Methodist: 2812
        Methodist (So Described): 2812
        Methodist Christian: 2812
        Methodist Church: 2812
        Methodist Church of Ghana: 2812
        Methodist Presbyterian Congregational: 2331
        Methodist Samoan: 2806
        Methodist United: 2813
        Metropolitan Community Churches: 2999
        Mission Church: 2999
        Mithraist: 6998
        Mixture: 7301
        Modern Orthodox: 5011
        Modern Orthodox Jewish: 5011
        Modern Spiritualist: 6171
        Molokan: 2999
        Monotheist: 7304
        Montenegro Orthodox: 2239
        Moon Worship: 6139
        Moonist: 6999
        Moravian: 2899
        Mormon Christian: 2151
        Mormon Lds: 2151
        Mosaiah: 5011
        Mosaic: 5011
        Moses: 5011
        Movement For Religious Renewal: 6996
        Movement For Spiritual Inner Awareness: 6999
        Multi Faith: 7301
        Muslim: 4011
        Muslim Alawi: 4011
        Muslim- Alevi: 4011
        Muslim Faith: 4011
        Muslim Islam: 4011
        Muslim Shia: 4011
        Muslim Sunni: 4011
        My Own Belief: 7303
        Mystical Spirituality: 7399
        Naming Ceremony: 7000
        Native American: 6139
        Native American Totemism: 6139
        Natural Spirituality: 7303
        Nature Religions : 6130
        Nature Religions, nec: 6139
        Nature Worship: 6139
        Nederland Hervormde: 2252
        Neo Heathen: 6139
        Neo Pagan: 6133
        Neo Paganism: 6133
        Neotaoist: 6053
        Nestorian: 2221
        New Age: 7302
        New Age and  Buddhism: 7301
        New Age Spiritual: 7302
        New Age Spirituality: 7302
        New Apostolic Christian: 2906
        New Apostolic Church: 2906
        New Churches Swedenborgian: 2907
        New Testament Christian: 2000
        New Thought Movement: 2911
        New Zealand Methodist: 2812
        Nichiren Buddhism: 1011
        Nirankari: 6151
        No Affiliated Religion: 7101
        No Comment: 0001
        No Religion: 7101
        No Religion (So Described): 7101
        Non Conformist Christian/Druid: 7301
        Non Denominational Church of Christ: 2112
        Non Denominational Pentecostal: 2400
        None of Your Business: 0001
        Nordic Gods: 6139
        Norse Heathen: 6139
        Northern Lakes Evangelical Church: 2807
        Norwegian Lutheran: 2171
        Not Christened Yet Catholic: 2071
        Not Stated: 0001
        Not Yet Baptised: 2000
        Not Yet Christened: 2000
        Nullafidian: 0000
        Occultist: 6999
        Old Apostolic Church Australia: 2999
        Old Gaelic: 6139
        Old Norse: 6139
        Old Russian Orthodox: 2239
        Olympian Gods: 6999
        One Humanity: 7399
        Optional Question: 0001
        Orange People: 6999
        Order of the Holy Cross: 2999
        Oriental Orthodox : 2210
        Oriental Orthodox, nec: 2219
        Orthodox Albanian: 2231
        Orthodox Christian Greek: 2233
        Orthodox Church of Christ the King: 6996
        Orthodox Eritrea: 2219
        Orthodox Evangelical Lutheran Church: 2171
        Orthodox Greek: 2233
        Orthodox Judaism: 5011
        Orthodox Romanian: 2235
        Orthodox Russian: 2236
        Orthodox Serbian: 2237
        Orthodox Slavic: 2230
        Orthodox Syrian Christian: 2215
        Orthodox Ukrainian: 2238
        Osho Sanvassin: 6999
        Other Anglican: 2019
        Other Christian : 2900
        Other Christian, nec: 2999
        Other Protestant: 2800
        Other Protestant, nec: 2899
        Other Religions: 6000
        Other Spiritual Beliefs : 7300
        Other Spiritual Beliefs, nec: 7399
        Own Set of Beliefs: 7303
        Own Spiritual Beliefs: 7303
        Own Spiritual Path: 7303
        Pagan: 6133
        Pagan - Asatru: 6139
        Paganism: 6133
        Paimarire: 2999
        Pantheism: 7304
        Pantheist: 7304
        Pelagian Christian: 2999
        Pentecostal Revival Fellowship: 2413
        Pentecostal: 2400
        Pentecostal A2A: 2416
        Pentecostal Apostolic: 2401
        Pentecostal Australian Christian Churches: 2402
        Pentecostal Born Again Christian: 2400
        Pentecostal C3: 2404
        Pentecostal CCC: 2404
        Pentecostal Christian AOG : 2402
        Pentecostal Christian Churches of Australia : 2417
        Pentecostal Christian City Church: 2404
        Pentecostal CRC Churches International: 2407
        Pentecostal Victory Worship Centre: 2423
        Pentecostal, nec: 2499
        Penteuchal: 2999
        Personal Blend: 7301
        Personal Eclectic Spiritual: 7301
        Personal Life Philosophy: 7200
        Personal Spiritual Path: 7303
        Philadelphia Church of God: 2999
        Philippine Independent Church: 2012
        Pilbara Aboriginal Apostolic Church: 2401
        Pirate: 0000
        Planetarian: 7399
        Pluralist: 7301
        Plymouth Brethren: 2051
        Plymouth Brethren Christian Church: 2051
        Polish Catholic: 2071
        Polish Orthodox: 2239
        Positivism: 7299
        Potters House Christian Centre: 2499
        Praise Evangelical Free Church: 2806
        Pravoslav Serbian: 2237
        Presbyterian: 2251
        Presbyterian and Reformed: 2250
        Presbyterian Church: 2251
        Presbyterian Church of Eastern Australia: 2251
        Presbyterian Church of Scotland: 2251
        Presbyterian Deaf Ministry: 2251
        Presbyterian Free Church of Australia: 2251
        Presbyterian Reformed: 2251
        Presbyterian Reformed Church of Australia: 2251
        Progressive Jewish: 5011
        Progressive Judaism: 5011
        Protestant Apostolic: 2901
        Protestant Reformed Church: 2252
        Protestantism: 2800
        Quaker: 2912
        Raja Yoga: 3011
        Ras Tafari: 6994
        Rastafari: 6994
        Ratana: 2908
        Ratana Maori: 2908
        Rational: 7204
        Rationalism: 7204
        Rationalist: 7204
        Ravidasia: 6151
        Reach Out For Christ Churches: 2499
        Rechabite Order: 2999
        Reformatic Evangelical: 2800
        Reformed: 2252
        Reformed Calvinist Church: 2252
        Reformed Church of Africa: 2252
        Reformed Church of Australia: 2252
        Reformed Evangelical: 2252
        Reformed Independent: 2252
        Reformed Presbyterian: 2251
        Reformed Protestant: 2250
        Reiki: 7399
        Reincarnation: 7399
        Religious Science: 2911
        Religion of Life: 7399
        Religious Belief: 7300
        Religious Groups, nec: 6999
        Religious Society of Friends (Quakers): 2912
        Remonstrant: 2805
        Reorganised Church of Jesus Christ Latter Day Saints: 2152
        Restoration Church of Jesus Christ: 2999
        Restoration Ministries: 2899
        Restored Apostolic Sending Congregations: 2999
        Restored Church of God: 2915
        Revival Centres: 2413
        Revival Centres Fellowship: 2421
        Revival Centres International: 2413
        Revival Crusade: 2407
        Revival Fellowship: 2421
        Revival House Church: 2421
        Revivalist: 2400
        Rhema: 2414
        Rhema Bible Church: 2414
        Rhema Family Church: 2414
        Rhema Pentecostal: 2414
        Rhema Word of Faith: 2414
        Ringatu: 6999
        Roman Catholic: 2071
        Romanian Orthodox: 2235
        Rosicrucian: 7399
        Rumanian Orthodox: 2235
        Russian Orthodox: 2236
        Russian Orthodox Church: 2236
        Russian Old Orthodox: 2239
        Sabian: 6901
        Sabian Mandaean: 6901
        Sacred Feminine: 7399
        Sahaja Yoga: 3011
        Sai Baba: 3011
        Saints: 7399
        Salvation Army: 2271
        Salvation Army Christian: 2271
        Samoa Worship Centre Christian Church: 2423
        Samoan Christian Fellowship: 2402
        Samoan Methodist Church: 2806
        Samoan Methodist Church of Australia: 2806
        Samoan Uniting Church: 2331
        Samoan Unity Christian Church: 2806
        Samoan Victory Worship Centre: 2423
        Sanatana Dharma: 3011
        Sat Sang: 6993
        Satan Worshipper: 6995
        Satanic: 6995
        Satanism: 6995
        Satanist: 6995
        Science and Reason: 7204
        Science of Mind: 2911
        Scientologist: 6992
        Scientology: 6992
        Scottish Presbyterian: 2251
        Secular Beliefs : 7200
        Secular Beliefs and Other Spiritual Beliefs and No Religious Affiliation : 7000
        Secular Beliefs, nec: 7299
        Secular Humanist: 7203
        Secularist: 7200
        Self Realisation: 0000
        Serbian Eastern Orthodox Church: 2237
        Serbian Orthodox: 2237
        Serbian Orthodox Christian: 2237
        Serbian Orthodox Church: 2237
        Seventh Day Adventist Christian: 2311
        Seventh Day Adventist Church: 2311
        Seventh Day Adventist Reform Movement: 2311
        Seventh-day Adventist: 2311
        Shaker: 2999
        Shamanism: 6139
        Shamanist: 6139
        Shaolin: 1011
        Sharman: 6139
        Shia: 4011
        Shiite: 4011
        Shinto: 6111
        Shinto Buddhist: 7301
        Shintoh: 6111
        Shintoism: 6111
        Shiva: 3011
        Siddha Yoga: 3011
        Signs Church: 2400
        Sikh: 6151
        Sikh Punjabi: 6151
        Sikh Religion: 6151
        Sikhism: 6151
        Sikhs: 6151
        Simple Christian: 2000
        Simple Christianty: 2000
        Singularitarianism: 7299
        Slavic Church: 2999
        Slavic Evangelical Pentecostal Churches of Australia: 2499
        Slavic Orthodox: 2230
        Slavic Pentecostal Church: 2499
        Social Humanism: 7203
        Society of Friends: 2912
        Society of Pius X: 2079
        Soka Gakkai: 1011
        Soto Zen: 1011
        Sound of God: 6993
        Southern Baptist: 2031
        Southern Cross Association of Churches: 2899
        Spiritism Kardec: 6171
        Spiritual Christian Molokan: 2999
        Spiritual Ecclectic: 7301
        Spiritual Enlightment: 7399
        Spiritual Evolution: 7399
        Spiritual Guru: 7399
        Spiritual Mix of East & West: 7301
        Spiritualism: 6171
        Spiritualist: 6171
        Spiritualist Church: 6171
        Spiritualist Union: 6171
        Spiritualistic Theosophist: 6996
        St Thomas Catholic: 2076
        Steiner: 6996
        Stoicism: 0000
        Strictly Orthodox Jew: 5011
        Subud: 6999
        Sudanese Orthodox Coptic: 2214
        Sufi: 4011
        Sufi Buddhist: 7301
        Sufism: 4011
        Sukyo Mahikari: 6112
        Sun Worshipper: 6139
        Sunni: 4011
        Svetosavska Orthodox Faith: 2237
        Swami: 3011
        Swarminarayan: 3011
        Swedish Lutheran: 2171
        Swedish Protestant: 2171
        Swiss Reformed Church: 2252
        Sydney Anglican: 2012
        Synagogue: 5011
        Syncretic: 7301
        Syriac Orthodox Church: 2215
        Syrian Christian Marthoma: 2215
        Syrian Jacobite: 2215
        Syrian Orthodox: 2215
        Syro Malabar Catholic: 2076
        Taoism: 6053
        Taoist: 6053
        Teaching of the Ascended Masters: 6996
        Technology: 7299
        Templar Society of Australia: 2913
        Temple Society: 2913
        Tenrikyo: 6113
        The Christian Community in Australia Movement For Renewal: 6996
        The Christian Community Renewal Movement: 6996
        The Church of Jesus Christ of Latter-day Saints: 2151
        The Family: 2999
        The Sun: 6139
        Theism: 7304
        Theist: 7304
        Theosophical Fellowship Church of Maitreya: 6996
        Theosophical Philosophy: 6996
        Theosophical Society: 6996
        Theosophist: 6996
        Theosophy: 6996
        Theravada: 1011
        Thien: 1011
        Tian Dao: 6053
        Tibetan Buddhism: 1011
        Tien Tao: 6053
        Tokaikolo Christian Church: 2806
        Tongan Christian Church: 2806
        Tongan Tokaikolo Church: 2806
        Tongan Weslyan Church in Aust: 2806
        Toowoomba Bible Truth Fellowship: 2899
        Torah True : 5011
        Torres Strait and Kaiwalagal Church: 2013
        Totem: 6999
        Traditional : 6999
        Traditional Aboriginal Belief: 6011
        Traditional Anglican Communion: 2012
        Traditional Belief: 6999
        Traditionalist Catholic: 2079
        Tridentine Catholic: 2079
        Trinity Church: 2999
        Uindy: 3011
        Ukrainian Autocephalic Orthodox Church: 2238
        Ukrainian Native Faith: 6999
        Ukrainian Orthodox: 2238
        Ukrainian Orthodox Christian: 2238
        Ukrainian Orthodox Church: 2238
        Ukranian Catholic: 2074
        Ultra Orthodox Jew: 5011
        Uniat Catholic: 2079
        Unification Church: 6999
        Unitarian Christian: 2999
        Unitarian Druse: 6071
        Unitarian Druze: 6071
        Unitarian Universalism: 7305
        Unitarian Universalist: 7305
        United Aborigines Mission: 2801
        United Church of Australia: 2331
        United Church of Canada: 2899
        United Church of Christ: 2899
        United Church of God: 2999
        United Church of Tonga: 2806
        United Methodist: 2813
        United Methodist Church: 2813
        United Mission Churches of Australia: 2899
        United Pentecostal: 2415
        United Pentecostal Christian: 2415
        United Pentecostal Church: 2415
        United Pentecostal Church of Australia: 2415
        United Reformed Church: 2805
        United Spiritualism in Australia: 6171
        Uniting: 2331
        Uniting Aboriginal Mission: 2801
        Uniting Church: 2331
        Uniting Church of Australia: 2331
        Uniting Deaf Ministry: 2331
        Unity School of Christianity: 2911
        Universal Brotherhood Faithists: 6999
        Universal Brotherhood Mission: 6151
        Universal Brotherhood of Faithists: 6999
        Universal Church: 2915
        Universal Love: 6996
        Universal Spiritual Values: 7399
        Unknown: 0000
        Unorthodox: 0000
        Urantian: 6999
        Vaishnavite: 3011
        Vaisnavism: 3011
        Various: 7301
        Various Beliefs: 7301
        Veda: 3011
        Vedic: 3011
        Veershaiva: 3011
        Vegetarian: 0000
        Verdantic: 3011
        Victorian Spiritualist Union: 6171
        Victory Life Centre: 2422
        Victory Life Church: 2422
        Victory Life Fellowship: 2422
        Victory Life Pentecostal: 2422
        Victory Life River Church: 2422
        Victory Worship Centre: 2423
        Vietnamese Evangelical Church in Australia: 2806
        Vineyard Christian Churches: 2000
        Vineyard Christian Fellowship: 2899
        Vipassana Buddhism: 1011
        Vipassana Meditation: 1011
        Vispassana: 1011
        Voodoo: 6139
        Vulcan: 6999
        Waverley Life Pentecostal: 2418
        Welsh Baptist: 2031
        Welsh Calvinistic Methodist: 2251
        Welsh Chapel: 2251
        Welsh Congregational: 2805
        Welsh Independent: 2805
        Welsh Methodist: 2251
        Welsh Presbyterian: 2251
        Wesley Central Mission: 2331
        Wesley Church: 2331
        Wesley Methodist: 2808
        Wesleyan Methodist: 2808
        Wesleyan Methodist Australia: 2808
        Wesleyan Methodist Church: 2808
        Weslyan Church of Tonga in Australia: 2806
        Western Catholic: 2071
        Westminster Presbyterian Church: 2251
        White Eagle Lodge: 6171
        White Lighter: 6171
        Wicca: 6135
        Wiccan: 6135
        Wiccan (Witchcraft): 6135
        Wiccan Spiritualist: 6135
        Witch: 6135
        Witchcraft: 6135
        Wong Tai Sin: 1011
        World Church of God: 2915
        World Goodwill: 6996
        World Harvest Ministries: 2422
        World Wide Churches: 2915
        Worldwide Church of God: 2915
        Worship Centre Christian Church: 2424
        Worship Centre Church: 2424
        Worship Centre Network : 2424
        Worship Centre Pentecostal Network: 2424
        Xtian: 2000
        Yazidi: 6902
        Yazidism: 6902
        Yezidi: 6902
        Yezidism: 6902
        Yezidist: 6902
        Zarathustrian: 6998
        Zen Buddhist: 1011
        Zoroastrian: 6998
        Zoroastrian Parsi: 6998
        Zoroastrianism: 6998
        Zwingli Swiss Protestant: 2252
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