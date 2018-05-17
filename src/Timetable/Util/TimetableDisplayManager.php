<?php
/**
 * Created by PhpStorm.
 *
 * This file is part of the Busybee Project.
 *
 * (c) Craig Rayner <craig@craigrayner.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * User: craig
 * Date: 9/05/2018
 * Time: 18:01
 */

namespace App\Timetable\Util;

use App\Calendar\Util\CalendarManager;
use App\Core\Manager\MessageManager;
use App\Core\Manager\SettingManager;
use App\Core\Manager\TwigManager;
use App\Core\Organism\Message;
use App\People\Util\PersonManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class TimetableDisplayManager extends TimetableManager
{
    /**
     * @var string
     */
    private $title = 'timetable.display.title';

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return TimetableDisplayManager
     */
    public function setTitle(string $title): TimetableDisplayManager
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return false|string
     */
    public function getTimetableDisplayDate()
    {
        return date('Ymd');
    }

    /**
     * Generate TimeTable
     *
     * @param $identifier
     * @param $displayDate
     */
    public function generateTimeTable($identifier, $displayDate)
    {
        if (false === $this->isValidTimetable() || empty($identifier))
            return;

        if (!$this->parseIdentifier($identifier))
            return;

        $this->getSession()->set('tt_displayDate', $displayDate);

        $this->setDisplayDate(new \DateTime($displayDate))
            ->generateWeeks();

        $dayDate = $this->getDisplayDate()->format('Ymd');
        foreach ($this->getWeeks() as $q => $week) {
            if ($week->start->format('Ymd') <= $dayDate && $week->finish->format('Ymd') >= $dayDate) {
                $this->setWeek($week);
                break;
            }
        }
        $this->mapCalendarWeek();

        $actSearch = 'generate' . ucfirst($this->gettype()) . 'Activities';
        foreach ($this->getWeek()->days as $q => $day) {
            $day->class = '';
            foreach ($day->ttday->getPeriods() as $p => $period)
                $period->activity = $this->$actSearch($period);
            if (isset($day->specialDay))
                $day = $this->manageSpecialDay($day);
        }

        $this->today = new \DateTime('today');
    }

    /**
     * @var string
     */
    private $header = '';

    /**
     * @return string
     */
    public function getHeader(): string
    {
        return $this->header = empty($this->header) ? 'timetable.header.blank' : $this->header;
    }

    /**
     * @param string $header
     * @return TimetableDisplayManager
     */
    public function setHeader(string $header): TimetableDisplayManager
    {
        $this->header = $header;
        return $this;
    }

    /**
     * @var string
     */
    private $idDesc;

    /**
     * @return string
     */
    public function getIdDesc(): string
    {
        return $this->idDesc ?: '';
    }

    /**
     * @param string $idDesc
     * @return TimetableDisplayManager
     */
    public function setIdDesc(string $idDesc): TimetableDisplayManager
    {
        $this->idDesc = $idDesc;
        return $this;
    }

    /**
     * @var \DateTime
     */
    private $displayDate;

    /**
     * @return \DateTime
     */
    public function getDisplayDate(): \DateTime
    {
        $this->displayDate = $this->displayDate instanceof \DateTime ? $this->displayDate : new \DateTime();
        return $this->displayDate;
    }

    /**
     * @param \DateTime $displayDate
     * @return TimetableDisplayManager
     */
    public function setDisplayDate(\DateTime $displayDate): TimeTableDisplayManager
    {
        if ($displayDate < $this->getCalendar()->getFirstDay())
            $displayDate = $this->getCalendar()->getFirstDay();

        if ($displayDate > $this->getCalendar()->getLastDay())
            $displayDate = $this->getCalendar()->getLastDay();

        $this->displayDate = $displayDate;

        return $this;
    }

    /**
     * @var \stdClass
     */
    private $week;

    /**
     * @return \stdClass
     */
    public function getWeek(): \stdClass
    {
        $this->week = $this->week instanceof \stdClass ? $this->week : new \stdClass();
        return $this->week;
    }

    /**
     * @param \stdClass $week
     * @return TimetableDisplayManager
     */
    public function setWeek(\stdClass $week): TimetableDisplayManager
    {
        $this->week = $week;
        return $this;
    }

    /**
     * @var bool
     */
    private $displayTimetable = true;

    /**
     * Parse Identifier
     * @param string $identifier
     * @return bool
     */
    protected function parseIdentifier(string $identifier): bool
    {
        if (false === $this->isDisplayTimetable())
            return $this->isDisplayTimetable();

        if (is_null($identifier) || strlen($identifier) < 5)
            return $this->setDisplayTimetable(false)->isDisplayTimetable();

        if (!$this->setType(substr($identifier, 0, 4)))
            return $this->isTimeTable = false;

        if (!$this->setIdentifier(substr($identifier, 4)))
            return $this->isTimeTable = false;

        $this->header = 'timetable.header.blank';
        switch ($this->getType()) {
            case 'Grade':
                if (empty($this->getIdDesc())) {
                    $this->setIdDesc($this->getOm()->getRepository(CalendarGroup::class)->findOneByGrade($this->getIdentifier())->getName());
                }
                $this->header = 'timetable.header.grade';
                break;
            case 'Student':
                $this->studentIdentifier = $this->getIdentifier();
                if (empty($this->getIdDesc())) {
                    $this->setIdDesc($this->getOm()->getRepository(Student::class)->find($this->studentIdentifier)->formatName(['surnameFirst' => false, 'preferredOnly' => true]));
                }
                $this->header = 'timetable.header.student';
                break;
            case 'Staff':
                $this->staffIdentifier = $this->getIdentifier();
                if (empty($this->getIdDesc())) {
                    $this->setIdDesc($this->getOm()->getRepository(Staff::class)->find($this->staffIdentifier)->formatName(['surnameFirst' => true, 'preferredOnly' => true]));
                }
                $this->header = 'timetable.header.staff';
                break;
            case 'Space':
                $this->spaceIdentifier = $this->getIdentifier();
                if (empty($this->getIdDesc())) {
                    $this->setIdDesc($this->getOm()->getRepository(Space::class)->find($this->spaceIdentifier)->getNameCapacity());
                }
                $this->header = 'timetable.header.space';
                break;
            default:
                throw new Exception('The TimeTable Type ' . $this->getType() . ' is not defined.');
        }
        $this->isTimeTable = true;

        $this->getSession()->set('tt_identifier', $identifier);

        return $this->isTimeTable;
    }

    /**
     * @param bool $displayTimetable
     * @return TimetableDisplayManager
     */
    public function setDisplayTimetable(bool $displayTimetable): TimetableDisplayManager
    {
        $this->displayTimetable = $displayTimetable;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDisplayTimetable(): bool
    {
        if (!$this->isValidTimetable())
            $this->setDisplayTimetable(false);
        return $this->displayTimetable;
    }

    /**
     * @var string
     */
    private $type = '';

    /**
     * @var array
     */
    private $types = [
        'grad' => 'Grade',
        'stud' => 'Student',
        'staf' => 'Staff',
        'spac' => 'Space',
    ];

    /**
     * Get type
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Set Type
     *
     * @param string $type
     * @return bool
     */
    public function setType(string $type): bool
    {

        if (isset($this->types[$type])) {
            $this->type = $this->types[$type];
        } else
            $this->setDisplayTimetable(false);

        return true;
    }

    /**
     * @var PersonManager
     */
    private $personManager;

    /**
     * TimetableDisplayManager constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param MessageManager $messageManager
     * @param SettingManager $settingManager
     * @param RequestStack $stack
     * @param CalendarManager $calendarManager
     * @param PersonManager $personManager
     */
    public function __construct(EntityManagerInterface $entityManager, MessageManager $messageManager,
                                SettingManager $settingManager, RequestStack $stack, CalendarManager $calendarManager,
                                PersonManager $personManager)
    {
        $this->personManager = $personManager;
        parent::__construct($entityManager, $messageManager, $settingManager, $stack, $calendarManager);
    }

    /**
     * @return PersonManager
     */
    public function getPersonManager(): PersonManager
    {
        return $this->personManager;
    }

    /**
     * @var string
     */
    private $description = 'timetable.display.description';

    /**
     * @param bool $translated
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function getDescription(bool $translated = false): string
    {
        return $translated ? TwigManager::renderMessage(new Message($this->description, ['%type%' => $this->getType(), '%identifier%' => $this->getIdDesc()], 'Timetable')) : $this->description;
    }
}