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
use App\Core\Util\UserManager;
use App\Entity\CalendarGrade;
use App\Entity\Space;
use App\Entity\Staff;
use App\Entity\Student;
use App\Entity\TimetableAssignedDay;
use App\Entity\TimetablePeriod;
use App\Entity\TimetablePeriodActivity;
use App\People\Util\PersonManager;
use App\Timetable\Organism\TimetableDay;
use App\Timetable\Organism\TimetableDisplayPeriod;
use App\Timetable\Organism\TimetableWeek;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
            if ($week->getStart()->format('Ymd') <= $dayDate && $week->getFinish()->format('Ymd') >= $dayDate) {
                $this->setWeek($week);
                break;
            }
        }

        $actSearch = 'generate' . ucfirst($this->gettype()) . 'Activities';
        foreach ($this->getWeek()->getDays() as $q => $day) {
            foreach ($day->getDay()->getPeriods() as $p => $period) {
                $day->addPeriod(new TimetableDisplayPeriod($period));
                $this->$actSearch($day, $period);
            }
            if (isset($day->specialDay))
                $this->manageSpecialDay($day);
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
     * @var TimetableWeek
     */
    private $week;

    /**
     * @return \stdClass
     */
    public function getWeek(): TimetableWeek
    {
        $this->week = $this->week instanceof TimetableWeek ? $this->week : new TimetableWeek();
        return $this->week;
    }

    /**
     * @param \stdClass $week
     * @return TimetableDisplayManager
     */
    public function setWeek(TimetableWeek $week): TimetableDisplayManager
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
                    $this->setIdDesc($this->getEntityManager()->getRepository(CalendarGrade::class)->find($this->getIdentifier())->getFullName());
                }
                $this->header = 'timetable.header.grade';
                break;
            case 'Student':
                $this->studentIdentifier = $this->getIdentifier();
                if (empty($this->getIdDesc())) {
                    $this->setIdDesc($this->getEntityManager()->getRepository(Student::class)->find($this->studentIdentifier)->formatName(['surnameFirst' => false, 'preferredOnly' => true]));
                }
                $this->header = 'timetable.header.student';
                break;
            case 'Staff':
                $this->setStaffIdentifier($this->getIdentifier());
                if (empty($this->getIdDesc())) {
                    $this->setIdDesc($this->getEntityManager()->getRepository(Staff::class)->find($this->staffIdentifier)->formatName(['surnameFirst' => true, 'preferredOnly' => true]));
                }
                $this->header = 'timetable.header.staff';
                break;
            case 'Space':
                $this->spaceIdentifier = $this->getIdentifier();
                if (empty($this->getIdDesc())) {
                    $this->setIdDesc($this->getEntityManager()->getRepository(Space::class)->find($this->spaceIdentifier)->getNameCapacity());
                }
                $this->header = 'timetable.header.space';
                break;
            default:
                throw new \TypeError('The TimeTable Type ' . $this->getType() . ' is not defined.');
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
     * @var UserManager
     */
    private $userManager;

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
                                PersonManager $personManager, UserManager $userManager)
    {
        $this->personManager = $personManager;
        $this->userManager = $userManager;
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

    /**
     * @var string|null
     */
    private $identifier;

    /**
     * @return string|null
     */
    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    /**
     * Set Identifier
     *
     * @param string $identifier
     * @return TimetableDisplayManager
     */
    public function setIdentifier(string $identifier): TimetableDisplayManager
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Generate Weeks
     *
     * @return TimeTableDisplayManager
     */
    private function generateWeeks(): TimeTableDisplayManager
    {
        if (false === $this->isDisplayTimetable())
            return $this;
        $this->clearWeeks();
        $weekNum = 0;

        $days = $this->getEntityManager()->getRepository(TimetableAssignedDay::class)->findBy(['timetable' => $this->getTimetable()], ['day' => 'ASC']);

        foreach($days as $ttDay)
        {
            if ($weekNum === 0)
            {
                $weekNum = $ttDay->getWeek();
                $week = new TimetableWeek();
            }
            if ($weekNum !== $ttDay->getWeek())
            {
                $this->addWeek($week);
                $week = new TimetableWeek();
                $weekNum = $ttDay->getWeek();
            }
            $day = new TimetableDay();
            $day->setDay($ttDay);
            $week->setStart($ttDay->getDay());
            $week->setFinish($ttDay->getDay());
            $week->setNumber($ttDay->getWeek());

            $day->setDate($ttDay->getDay());
            $week->addDay($day);
            $this->getWeeks($week);
        }

        return $this;
    }

    /**
     * @return TimeTableDisplayManager
     */
    public function clearWeeks(): TimeTableDisplayManager
    {
        $this->setWeeks(new ArrayCollection());

        return $this;
    }

    /**
     * @param \stdClass $week
     * @return TimeTableManager
     */
    public function addWeek(TimetableWeek $week): TimetableDisplayManager
    {
        if (empty($week))
            return $this;
        // remove none school days
        foreach ($week->getDays() as $q => $day) {
            if (!in_array($day->getDate()->format('D'), $this->getSchoolWeek()))
                $week->removeDay($day);
        }

        if ($week->getDays()->count() > 0) {
            $day = $week->getDays()->first()->getDate()->format('Ymd');
            $this->getWeeks()->set($day, $week);
        }

        return $this;
    }

    /**
     * getTimeTableIdentifier
     *
     * @return null|string
     * @throws \Doctrine\ORM\ORMException
     */
    public function getTimeTableIdentifier(): ?string
    {
        // Determine if user is staff or student
        if (!$this->getUserManager()->hasPerson()) {
            if ($this->getSession()->has('tt_identifier'))
                $this->getSession()->remove('tt_identifier');
            return null;
        }
        $identifier = '';

        if ($this->getPersonManager()->isStudent($this->getUserManager()->getPerson())) {
            $identifier = 'stud' . $this->getUserManager()->getPerson()->getId();
        }
        if ($this->personManager->isStaff($this->getUserManager()->getPerson())) {
            $identifier = 'staf' . $this->getUserManager()->getPerson()->getId();
        }
        $this->getSession()->set('tt_identifier', $identifier);

        return $identifier;
    }

    /**
     * @return UserManager
     */
    public function getUserManager(): UserManager
    {
        return $this->userManager;
    }

    /**
     * @var integer
     */
    private $staffIdentifier;

    /**
     * @return int
     */
    public function getStaffIdentifier(): int
    {
        return $this->staffIdentifier;
    }

    /**
     * @param int $staffIdentifier
     * @return TimetableDisplayManager
     */
    public function setStaffIdentifier(int $staffIdentifier): TimetableDisplayManager
    {
        $this->staffIdentifier = $staffIdentifier;
        return $this;
    }

    /**
     * generateStaffActivities
     *
     * @param TimetableDay $day
     * @param TimetablePeriod $period
     * @return TimetablePeriodActivity|null
     */
    private function generateStaffActivities(TimetableDay $day, TimetablePeriod $period): ?TimetablePeriodActivity
    {
        $activities = $this->getPeriodActivities($period);
        if ($activities->count() === 0) return null;

        $member = $this->getEntityManager()->getRepository(Staff::class)->find($this->getIdentifier());
        foreach($activities as $pa) {
            foreach ($pa->loadTutors() as $at)
                if ($member->isEqualTo($at->getTutor()))
                    $day->setPeriodActivity($period, $pa);
        }
        return null;
    }

    /**
     * getPeriodActivities
     *
     * @param TimetablePeriod $period
     * @return Collection
     */
    private function getPeriodActivities(TimetablePeriod $period): Collection
    {
        $activities = $this->getEntityManager()->getRepository(TimetablePeriodActivity::class)->findByPeriod($period);
        if (empty($activities))
            return new ArrayCollection();
        return new ArrayCollection($activities);
    }

    /**
     * getDayHours
     *
     * @return array
     * @throws \Exception
     */
    public function getDayHours()
    {
        $hours = [];
        $time = $this->getSettingManager()->get('schoolday.begin');
        $finish = $this->getSettingManager()->get('schoolday.finish');

        foreach($this->getWeek()->getDays() as $day) {
            foreach ($day->getDay()->getPeriods() as $period) {
                if ($period->getStart() < $time)
                    $time = $period->getStart();
                if ($period->getEnd() > $finish)
                    $finish = $period->getEnd();
            }
        }

        do {
            $hours[] = $time->format($this->getSettingManager()->get('time.format.short'));

            $time->add(new \DateInterval('PT1H'));

        } while ($time < $finish);

        return $hours;
    }

    /**
     * isCurrentTime
     *
     * @param \DateTime $day
     * @param TimetablePeriod $period
     * @return bool
     */
    public function isCurrentTime(\DateTime $day, TimetablePeriod $period): bool
    {
        if ($day->format('Ymd') === date('Ymd')) {
            if ($period->getStart()->format('Hi') <= date('Hi') && $period->getEnd()->format('Hi') > date('Hi'))
                return true;
        }

        return false;
    }

    /**
     * getClass
     *
     * @param TimetableDisplayPeriod $period
     * @param \DateTime $date
     * @return string
     */
    public function getClass(TimetableDisplayPeriod $period, \DateTime $date): string
    {
        $class = 'calendarPeriod';
        if ($period->isBreak())
            $class .= ' calendarBreak';
        if ($period->isActive())
            $class .= ' calendarActive';
        if ($this->isCurrentTime($date, $period->getPeriod()))
            $class .= ' calendarCurrent';
        return trim($class . ' ' . $period->getClass());
    }
}