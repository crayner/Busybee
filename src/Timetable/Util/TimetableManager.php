<?php
namespace App\Timetable\Util;

use App\Calendar\Util\CalendarManager;
use App\Core\Manager\MessageManager;
use App\Core\Manager\SettingManager;
use App\Core\Manager\TabManager;
use App\Entity\Calendar;
use App\Entity\CalendarGrade;
use App\Entity\SpecialDay;
use App\Entity\Term;
use App\Entity\Timetable;
use App\Entity\TimetableAssignedDay;
use App\Entity\TimetableColumn;
use App\Pagination\PeriodPagination;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Yaml\Yaml;

class TimetableManager extends TabManager
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var SettingManager
     */
    private $settingManager;

    /**
     * @var array
     */
    private $schoolWeek;

    /**
     * @var RequestStack
     */
    private $stack;

    /**
     * @var CalendarManager
     */
    private $calendarManager;

    /**
     * TimetableManager constructor.
     * @param EntityManagerInterface $entityManager
     * @param MessageManager $messageManager
     * @param SettingManager $settingManager
     * @param RequestStack $stack
     */
    public function __construct(EntityManagerInterface $entityManager, MessageManager $messageManager,
                                SettingManager $settingManager, RequestStack $stack, CalendarManager $calendarManager)
    {
        $this->entityManager = $entityManager;
        $this->messageManager = $messageManager;
        $this->settingManager = $settingManager;
        $this->schoolWeek = $this->getSettingManager()->get('schoolweek');
        $this->stack = $stack;
        $this->calendarManager = $calendarManager;
    }

    /**
     * @var Timetable|null
     */
    private $timetable;

    /**
     * @param $id
     * @return Timetable
     */
    public function find($id, $notEmpty = false): Timetable
    {
        $entity = $this->getEntityManager()->getRepository(Timetable::class)->find($id);

        if (! $entity instanceof Timetable && $notEmpty)
            throw new \InvalidArgumentException('The system must provide an existing timetable identifier.');
        elseif(! $entity instanceof Timetable)
            $entity = new Timetable();

        $this->timetable = $entity;

        return $entity;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * @return array
     */
    public function getTabs(): array
    {
        $x = Yaml::parse("
timetable:
    label: timetable.details.tab
    include: Timetable/display.html.twig
    translation: Timetable
");
        if ($this->isValidTimetable())
        {
            foreach($this->getCalendar()->getTerms() as $term)
            {
                $w = [];
                $w['label'] = 'timetable.term.tab';
                $w['label_params'] = ['%name%' => $term->getName()];
                $w['translation'] = 'Timetable';
                $w['include'] = 'Timetable/Day/assign_days.html.twig';
                $w['with'] = ['term' => $term];
                $w['display'] = ['method' =>'isValidTerm', 'with' => ['term' => $term, 'timetable' => $this->getTimetable()]];
                $x[$term->getName()] = $w;
            }
        }

        return $x;
    }

    /**
     * @return bool
     */
    public function isValidTimetable(): bool
    {
        if ($this->getTimetable() instanceof Timetable && $this->getTimetable()->getId() > 0)
            return true;
        return false;
    }

    /**
     * @return Timetable|null
     */
    public function getTimetable(): ?Timetable
    {
        return $this->timetable;
    }

    /**
     * @return Calendar
     */
    public function getCalendar(): Calendar
    {
        return $this->getTimetable()->getCalendar();
    }

    /**
     * @var string
     */
    private $status = 'default';

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return TimetableManager
     */
    public function setStatus(string $status): TimetableManager
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @var MessageManager
     */
    private $messageManager;

    /**
     * @return MessageManager
     */
    public function getMessageManager(): MessageManager
    {
        $this->messageManager->setDomain('Timetable');
        return $this->messageManager;
    }

    /**
     * @param mixed $id
     * @throws \Exception
     */
    public function createAssignedDays($id)
    {
        if ($id === 'Add')
            return;

        $this->find($id);

        $this->deleteTimetableAssignedDays();

        $day = clone $this->getCalendar()->getFirstDay();
        $days = new ArrayCollection();
        $weekNumber = 1;
        if ($day->format('l') === $this->getSettingManager()->get('firstdayofweek'))
            $weekNumber = 0;

        $this->getStartRotateDays();

        $this->getColumnDays();

        do {
            $assignDay = new TimetableAssignedDay();
            $assignDay->setDay(clone $day);
            $assignDay->setType('holiday');
            if ($day->format('l') === $this->getSettingManager()->get('firstdayofweek'))
                $weekNumber++;
            $assignDay->setWeek($weekNumber);
            $assignDay->setTimetable($this->getTimetable());

            foreach($this->getCalendar()->getTerms()->getIterator() as $term)
            {
                if ($day >= $term->getFirstDay() && $day <= $term->getLastDay())
                {
                    $assignDay->setTerm($term);
                    if (in_array($day->format('D'), $this->schoolWeek))
                        $assignDay->setType('school_day');
                    else
                        $assignDay->setType('no_school');
                }
            }

            if (isset($this->startRotateDays[$assignDay->getDay()->format('Ymd')]))
                $assignDay->setStartRotate(true);

            $assignDay->setColumn($this->mapDay($assignDay));

            $sd = $this->getEntityManager()->getRepository(SpecialDay::class)->findOneBy(['day' => $day]);
            if ($sd) {
                $assignDay->setType($sd->getType());
                $assignDay->setSpecialDay($sd);
            }
            $this->getEntityManager()->persist($assignDay);
            $day->add(new \DateInterval('P1D'));
        } while ($day <= $this->getCalendar()->getLastDay());

        $this->getEntityManager()->flush();
    }

    /**
     * Delete Timetable Assigned Days
     */
    private function deleteTimetableAssignedDays()
    {
        $this->getEntityManager()->getRepository(TimetableAssignedDay::class)->createQueryBuilder('a')
            ->where('a.timetable = :timetable')
            ->setParameter('timetable', $this->getTimetable())
            ->delete()
            ->getQuery()
            ->getResult();
    }

    /**
     * @return SettingManager
     */
    public function getSettingManager(): SettingManager
    {
        return $this->settingManager;
    }

    /**
     * @param bool $refresh
     * @return array
     * @throws \Exception
     */
    private function getStartRotateDays(bool $refresh = true): array
    {
        if (! empty($this->startRotateDays) && !$refresh)
            return $this->startRotateDays;


        foreach($this->getCalendar()->getTerms()->getIterator() as $term)
        {
            $day = clone $term->getFirstDay();
            while ($day->format('l') !== $this->getSettingManager()->get('firstdayofweek'))
                $day->sub(new \DateInterval('P1D'));

            $this->startRotateDays[$day->format('Ymd')] = $day;
        }
        if (empty($this->startRotateDays))
            $this->startRotateDays['19000101'] = new \DateTime('19700101');

        return $this->startRotateDays;
    }

    /**
     * @var array
     */
    private $rotateDays = [];

    /**
     * @var array
     */
    private $fixedDays = [];

    /**
     * @var bool
     */
    private $daysDefined = false;

    /**
     * @param bool $refresh
     */
    private function getColumnDays(bool $refresh = true)
    {
        if (! ($this->daysDefined || $refresh)) {
            reset($this->rotateDays);
            return;
        }
        $this->rotateDays = [];
        $this->fixedDays = [];

        $results = $this->getEntityManager()->getRepository(TimetableColumn::class)->findBy(['timetable' => $this->getTimetable()], ['sequence' => 'ASC']);

        foreach($results as $column)
        {
            if ($column->getMappingInfo() === 'rotate')
            {
                $this->rotateDays[$column->getSequence()] = $column;
            } else {
                $this->fixedDays[$column->getMappingInfo()] = $column;
            }
        }

        $this->daysDefined = true;

        reset($this->rotateDays);
    }

    /**
     * @param TimetableAssignedDay $day
     * @return TimetableColumn|null
     */
    private function mapDay(TimetableAssignedDay $day): ?TimetableColumn
    {
        $mappingInfo = $day->getDay()->format('D');
        if (! in_array($mappingInfo, $this->schoolWeek))
            return null;
        if (isset($this->fixedDays[$mappingInfo])) {
            if ($day->isStartRotate())
                end($this->rotateDays);
            return $this->fixedDays[$mappingInfo];
        }

        $column = next($this->rotateDays);
        if (false === $column || isset($this->startRotateDays[$day->getDay()->format('Ymd')]) || $day->isStartRotate())
            $column = reset($this->rotateDays);

        return $column;
    }

    /**
     * @param array $param
     * @return bool
     */
    public function isValidTerm(array $param): bool
    {
        $result = $this->getEntityManager()->getRepository(TimetableAssignedDay::class)->findBy(['timetable' => $param['timetable'], 'term' => $param['term']]);

        return $result ? true : false ;
    }

    /**
     * @param Term $term
     * @return array
     * @throws \Exception
     */
    public function getAssignedDays(Term $term): array
    {
        $days = $this->getEntityManager()->getRepository(TimetableAssignedDay::class)->findBy(['term' => $term]);

        $day = clone reset($days)->getDay();
        $week = reset($days)->getWeek();
        $fdow = $this->getSettingManager()->get('firstdayofweek');
        while ($day->format('l') !== $fdow)
        {
            $w = $day->sub(new \DateInterval('P1D'));
            $ad = $this->getEntityManager()->getRepository(TimetableAssignedDay::class)->findOneBy(['day' => $w, 'timetable' => $this->getTimetable()]);
            if ($ad) {
                $ad->setTerm($term);
            } else {
                $ad = new TimetableAssignedDay();
                $ad->setDay(clone $w);
                $ad->setType('no_school');
                $ad->setTerm($term);
                $ad->setWeek($week);
                $ad->setTimetable($this->getTimetable());
            }
            array_unshift($days, $ad);
            unset($ad);
            $day = clone $w;
        }

        $day = clone end($days)->getDay();
        $ldow = $fdow === 'Monday' ? 'Sunday' : 'Saturday' ;
        while ($day->format('l') !== $ldow)
        {
            $w = $day->add(new \DateInterval('P1D'));
            $ad = $this->getEntityManager()->getRepository(TimetableAssignedDay::class)->findOneBy(['day' => $w, 'timetable' => $this->getTimetable()]);
            if ($ad) {
                $ad->setTerm($term);
            } else {
                $ad = new TimetableAssignedDay();
                $ad->setDay(clone $w);
                $ad->setType('no_school');
                $ad->setTerm($term);
                $ad->setWeek($week);
                $ad->setTimetable($this->getTimetable());
            }
            $this->getEntityManager()->persist($ad);
            array_push($days, $ad);
            $day = clone $w;
        }

        $this->getEntityManager()->flush();
        $this->weeks = [];

        foreach($days as $day)
            $this->weeks[$day->getWeek()][] = $day;

        reset($days);
        $this->days = $days;
        return $this->days;
    }

    /**
     * @var array
     */
    private $days;

    /**
     * @var array
     */
    private $weeks;

    /**
     * @return array
     */
    public function getDays(): array
    {
        return $this->days;
    }

    /**
     * @return array
     */
    public function getWeeks(): array
    {
        return $this->weeks;
    }

    /**
     * @param $date
     * @return bool
     */
    public function testDate($date): bool
    {
        $date = new \DateTime($date);

        $result = $this->getEntityManager()->getRepository(TimetableAssignedDay::class)->findOneBy(['day' => $date, 'timetable' => $this->getTimetable()]);

        if (empty($result)) {
            $this->getMessageManager()->add('danger', 'timetable.rotate.toggle.failed');
            return false;
        }
        return true;
    }

    /**
     * @param string $date
     * @return TimetableAssignedDay|null
     * @throws \Exception
     */
    public function toggleRotateStart(string $date): ?TimetableAssignedDay
    {
        if (!$this->testDate($date))
            return null;

        $date = new \DateTime($date);

        $rd = $this->getEntityManager()->getRepository(TimetableAssignedDay::class)->findOneBy(['timetable' => $this->getTimetable(), 'day' => $date]);

        if ($this->getTimetable()->isLocked())
        {
            $this->getMessageManager()->add('warning', 'timetable.locked.true', [], 'Timetable');
            return $rd;
        }


        if ($rd) {
            $rd->setStartRotate($rd->isStartRotate() ? false : true);
            $this->getEntityManager()->persist($rd);
            $this->getEntityManager()->flush();
            $this->getMessageManager()->add('success', 'timetable.rotate.toggle.success', ['%{date}' => $date->format($this->getSettingManager()->get('date.format.long'))], 'Timetable');
        } else {
            $this->getMessageManager()->add('danger', 'timetable.rotate.toggle.failed', [], 'Timetable');
        }
        $this->refreshColumnDays($rd);

        return $rd;
    }

    /**
     * @param TimetableAssignedDay $day
     * @throws \Exception
     */
    private function refreshColumnDays(TimetableAssignedDay $day)
    {
        $this->getStartRotateDays(true);

        $this->getColumnDays();

        $days = $this->getAssignedDays($day->getTerm());

        foreach($days as $assignDay)
        {
            $assignDay->setColumn($this->mapDay($assignDay));
            $this->getEntityManager()->persist($assignDay);
        }
        $this->getEntityManager()->flush();
    }

    /**
     * @return Collection
     */
    public function getCalendarGrades(): Collection
    {
        return $this->getCalendar()->getCalendarGrades();
    }

    /**
     * @var null|\stdClass
     */
    private $report;

    /**
     * Get Report
     * @param PeriodPagination $pag
     * @return TimetableReportManager
     */
    public function getReport(PeriodPagination $pag)
    {
        if (!$this->timetable instanceof TimeTable)
            throw new \InvalidArgumentException('The timetable has not been injected into the manager.');

        $this->report = new TimetableReportManager();
        $this->report = $this->report->setEntityManager($this->getEntityManager())->retrieveCache($this->getTimetable(), TimetableReportManager::class);

        $this->report
            ->setGrades($this->getGrades())
            ->setCalendar($this->getCurrentCalendar())
            ->setSpaceTypes($this->getSettingManager()->get('space.type.teaching_space'))
            ->setPeriodList($pag->getResult())
        ;

        $this->report->saveReport();

        return $this->report;
    }

    /**
     * @var ArrayCollection
     */
    private $grades;

    /**
     * @return ArrayCollection
     */
    public function getGrades(): ArrayCollection
    {
        if (! empty($this->grades))
            return $this->grades;

        $grades = $this->getGradeControls();

        $results = $this->getEntityManager()->getRepository(CalendarGrade::class)->createQueryBuilder('cg')
            ->where('cg.calendar = :calendar')
            ->setParameter('calendar', $this->getCurrentCalendar())
            ->andWhere('cg.grade in (:grades)')
            ->setParameter('grades', $grades, Connection::PARAM_STR_ARRAY)
            ->orderBy('cg.sequence', 'ASC')
            ->getQuery()
            ->getResult();
        $this->grades = new ArrayCollection();
        foreach($results as $grade)
            $this->grades->set($grade->getId(), $grade);

        return $this->grades;
    }

    /**
     * @return array
     */
    public function getGradeControls(): array
    {
        $grades =  $this->getStack()->getCurrentRequest()->getSession()->has('gradeControl') ? $this->getStack()->getCurrentRequest()->getSession()->get('gradeControl') : [];
        $x = [];
        foreach($grades as $q=>$w)
            if ($w)
                $x[] = $q;
        return $x;
    }

    /**
     * @return CalendarManager
     */
    public function getCalendarManager(): CalendarManager
    {
        return $this->calendarManager;
    }

    /**
     * @return Calendar
     */
    public function getCurrentCalendar(): Calendar
    {
        return $this->getCalendarManager()->getCurrentCalendar();
    }

    /**
     * @return RequestStack
     */
    public function getStack(): RequestStack
    {
        return $this->stack;
    }

}