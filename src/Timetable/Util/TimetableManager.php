<?php
namespace App\Timetable\Util;

use App\Core\Manager\MessageManager;
use App\Core\Manager\SettingManager;
use App\Core\Manager\TableManager;
use App\Core\Manager\TabManager;
use App\Core\Manager\TabManagerInterface;
use App\Entity\Calendar;
use App\Entity\SpecialDay;
use App\Entity\Staff;
use App\Entity\Term;
use App\Entity\Timetable;
use App\Entity\TimetableAssignedDay;
use App\Entity\TimetableColumn;
use App\Entity\TimetablePeriodActivity;
use App\Pagination\PeriodPagination;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Yaml\Yaml;

class TimetableManager extends TabManager
{
    /**
     * @var RequestStack
     */
    private $stack;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var MessageManager
     */
    private $messageManager;

    /**
     * @var string
     */
    private $status = 'default';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var null|Timetable
     */
    private $timetable;

    /**
     * @var SettingManager
     */
    private $settingManager;

    /**
     * @var array
     */
    private $schoolWeek;

    /**
     * @var PeriodManager 
     */
    private $periodManager;
    
    /**
     * TimetableManager constructor.
     * @param RequestStack $stack
     * @param RouterInterface $router
     */
    public function __construct(RequestStack $stack, RouterInterface $router,
                                MessageManager $messageManager, EntityManagerInterface $entityManager,
                                SettingManager $settingManager, PeriodManager $periodManager)
    {
        $this->stack = $stack;
        $this->router = $router;
        $this->messageManager = $messageManager;
        $this->entityManager = $entityManager;
        $this->settingManager = $settingManager;
        $this->schoolWeek = $this->getSettingManager()->get('schoolweek');
        $this->periodManager = $periodManager;
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
                $w['label'] = $term->getName();
                $w['translation'] = false;
                $w['include'] = 'Timetable/Day/assign_days.html.twig';
                $w['with'] = ['term' => $term];
                $w['display'] = ['method' =>'isValidTerm', 'with' => ['term' => $term, 'timetable' => $this->getTimetable()]];
                $x[$term->getName()] = $w;
            }
        }

        return $x;
    }

    /**
     * @return string
     */
    public function getCollectionScripts(): string
    {
        return '<!-- Collection Scripts -->';
    }

    /**
     * @return MessageManager
     */
    public function getMessageManager(): MessageManager
    {
        return $this->messageManager;
    }

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
     * @return Timetable|null
     */
    public function getTimetable(): ?Timetable
    {
        return $this->timetable;
    }

    /**
     * @return bool
     */
    public function isValidTimetable(): bool
    {
        if ($this->timetable instanceof Timetable && $this->timetable->getId() > 0)
            return true;
        return false;
    }

    /**
     * @param int $cid
     * @return null
     */
    public function removeColumn(int $cid)
    {
        $column = $this->getEntityManager()->getRepository(TimetableColumn::class)->find($cid);

        if (empty($column) || ! $this->getTimeTable()->getColumns()->contains($column)) {
            $this->getMessageManager()->add('warning', 'timetable.column.remove.missing', [], 'Timetable');
            return ;
        }

        if (!$column->canDelete()) {
            $this->getMessageManager()->add('warning', 'timetable.column.remove.locked', [], 'Timetable');
            return ;
        }

        $this->deleteTimetableAssignedDays();

        try {
            $this->timetable->removeColumn($column);

            $this->getEntityManager()->persist($this->timetable);
            $this->getEntityManager()->remove($column);
            $this->getEntityManager()->flush();
        } catch (\Exception $e) {
            $this->getMessageManager()->add('danger', 'timetable.column.remove.error', ['%{message}' => $e->getMessage()], 'Timetable');
            return ;
        }

        $this->getMessageManager()->add('success', 'timetable.column.remove.success', [], "Timetable");

        return ;
    }

    /**
     * @var null|\stdClass
     */
    private $report;

    /**
     * Get Report
     * @param PeriodPagination $pag
     * @return null|\stdClass
     */
    public function getReport(PeriodPagination $pag)
    {
        if ($this->report instanceof \stdClass)
            return $this->report;

        return $this->generateReport($pag);
    }

    /**
     * Generate Report
     * @param PeriodPagination $pag
     * @return null|\stdClass
     */
    private function generateReport(PeriodPagination $pag)
    {
        if (!$this->timetable instanceof TimeTable)
            throw new \InvalidArgumentException('The timetable has not been injected into the manager.');

        $this->report = new TimetableReportManager($this->getTimetable(), $pag);

        $this->report->periods = [];
        $this->report->activities = [];
        $this->report->staff = [];

        foreach ($pag->getResult() as $period) {
            $per = new \stdClass();
            $per->status = $this->periodManager->setPeriod($period['entity'])->getPeriodStatus();
            $per->period = $period['entity'];
            $per->id = $period['id'];
            $per->name = $period['name'];
            $per->start = $period['start'];
            $per->end = $period['end'];
            $per->code = $period['code'];
            $per->columnName = $period['columnName'];
            $per->activities = [];

            $this->getPeriodManager();

            foreach ($per->period->getActivities() as $activity) {
                if ($this->activeGrade($activity)) {
                    $act = new \stdClass();
                    $act->activity = $activity;
                    $act->details = $this->periodManager->getActivityDetails($activity);
                    $act->status = $this->periodManager->getActivityStatus($activity);
                    $act->id = $activity->getId();
                    $act->fullName = $activity->getFullName();
                    $per->activities[] = $act;
                    if ($activity->getActivity() instanceof Activity) {
                        if (isset($this->report->activities[$activity->getActivity()->getId()])) {
                            $act = $this->report->activities[$activity->getActivity()->getId()];
                        } else {
                            $act = $activity->getActivity();
                        }

                        $this->report->activities[$activity->getActivity()->getId()] = $act;

                    }
                }

                foreach($activity->loadTutors()->getIterator() as $tutor)
                    $this->getStaffReport($tutor->getTutor(), $per);
            }

            $this->report->periods[] = $this->setPeriodStatusLevel($per);
            $per->status->messages = clone $this->getMessageManager();
            $this->getMessageManager()->clearMessages();
        }

        return $this->report;
    }

    /**
     * @return Collection
     */
    public function getCalendarGrades(): Collection
    {
        return $this->getCalendar()->getCalendarGrades();
    }

    /**
     * @return Calendar
     */
    public function getCalendar(): Calendar
    {
        return $this->getTimetable()->getCalendar();
    }

    /**
     * @param array $param
     * @return bool
     */
    public function isValidTerm(array $param): bool
    {
        $result = $this->getEntityManager()->getRepository(TimetableAssignedDay::class)->findBy(['timetable' => $param['timetable'], 'term' => $param['term']]);

        if ($result)
            return true;

        return false;
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
     * @return SettingManager
     */
    public function getSettingManager(): SettingManager
    {
        return $this->settingManager;
    }

    /**
     * @var array
     */
    private $startRotateDays = [];

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
            reset($this->daysDefined);
            return;
        }
        $this->rotateDays = [];
        $this->fixedDays = [];

        $results = $this->getEntityManager()->getRepository(TimetableColumn::class)->findBy(['timetable' => $this->getTimetable()], ['sequence' => 'ASC']);

        foreach($results as $column)
        {
            if ($column->getMappingInfo() === 'Rotate')
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
     * @var array
     */
    private $days;

    /**
     * @var array
     */
    private $weeks;

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
     * @return PeriodManager
     */
    public function getPeriodManager(): PeriodManager
    {
        return $this->periodManager;
    }

    /**
     * @param $per
     * @return mixed
     */
    private function setPeriodStatusLevel($per)
    {
        $per->status->alert = $this->getMessageManager()->getHighestLevel();

        return $per;
    }

    /**
     * @param TimetablePeriodActivity $activity
     * @return bool
     */
    private function activeGrade(TimetablePeriodActivity $activity): bool
    {
        $control = $this->getPeriodManager()->getGradeControl();
        foreach ($activity->getActivity()->getCalendarGrades()->getIterator() as $grade)
            if (!isset($control[$grade->getGrade()]) || $control[$grade->getGrade()])
                return true;

        return false;
    }

    /**
     * @return RequestStack
     */
    public function getStack(): RequestStack
    {
        return $this->stack;
    }

    /**
     * @param Staff $tutor
     * @param \stdClass $per
     */
    private function getStaffReport(Staff $tutor, \stdClass $per)
    {
        $id = $tutor->getId();
        $teachLoadLow = $this->getSettingManager()->get('teachingload.column.maximum', 2);
        $teachLoadHigh = $this->getSettingManager()->get('teachingload.column.maximum', 9);
        if (empty($this->report->staff[$id]['status']))
            $this->report->staff[$id]['status'] = 'ok';
        $this->report->staff[$id]['staff'] = $tutor;
        $this->report->staff[$id]['period'][$per->period->getColumn()->getId()][$per->id] = $per->period;
        $this->report->staff[$id]['total'] = empty($this->report->staff[$id]['total']) ? 1 : $this->report->staff[$id]['total'] + 1;
        if (count($this->report->staff[$id]['period'][$per->period->getColumn()->getId()]) == $teachLoadLow) {
            $this->getMessageManager()->add('info', 'teachingload.column.equal', ['%name%' => $tutor->formatName()], 'Timetable');
            $per->status->alert = 'info';
        }
        if ($this->report->staff[$id]['total'] == $teachLoadHigh) {
            $this->report->staff[$id]['status'] = 'info';
            $this->getMessageManager()->add('info', 'teachingload.timetable.equal', ['%name%' => $tutor->formatName()], 'Timetable');
        }
        if (count($this->report->staff[$id]['period'][$per->period->getColumn()->getId()]) > $teachLoadLow) {
            $this->report->staff[$id]['status'] = 'danger';
            $this->getMessageManager()->add('danger', 'teachingload.column.exceeded', ['%name%' => $tutor->formatName()], 'Timetable');
        }
        if ($this->report->staff[$id]['total'] > $teachLoadHigh) {
            $this->report->staff[$id]['status'] = 'danger';
            $this->getMessageManager()->add('danger', 'teachingload.timetable.exceeded', ['%name%' => $tutor->formatName()], 'Timetable');
        }
    }
}