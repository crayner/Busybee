<?php
namespace App\Timetable\Util;

use App\Core\Manager\MessageManager;
use App\Core\Manager\TableManager;
use App\Core\Manager\TabManager;
use App\Core\Manager\TabManagerInterface;
use App\Entity\Timetable;
use App\Entity\TimetableColumn;
use App\Pagination\PeriodPagination;
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
     * TimetableManager constructor.
     * @param RequestStack $stack
     * @param RouterInterface $router
     */
    public function __construct(RequestStack $stack, RouterInterface $router, MessageManager $messageManager, EntityManagerInterface $entityManager)
    {
        $this->stack = $stack;
        $this->router = $router;
        $this->messageManager = $messageManager;
        $this->entityManager = $entityManager;
    }

    /**
     * @return array
     */
    public function getTabs(): array
    {
        return Yaml::parse("
timetable:
    label: timetable.details.tab
    include: Timetable/display.html.twig
    translation: Timetable
");
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
    public function getTimeTable(): ?Timetable
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

        $this->report = new \stdClass();

        $this->report->periods = [];
        $this->report->activities = [];
        $this->report->staff = [];

        foreach ($pag->getResult() as $period) {
            $per = new \stdClass();
            $per->status = $this->pm->getPeriodStatus($period['id']);
            $per->period = $period['0'];
            $per->id = $period['id'];
            $per->name = $period['name'];
            $per->start = $period['start'];
            $per->end = $period['end'];
            $per->nameShort = $period['nameShort'];
            $per->columnName = $period['columnName'];
            $per->activities = [];

            $this->pm->clearResults();

            foreach ($per->period->getActivities() as $activity) {
                if ($this->activeGrade($activity)) {
                    $act = new \stdClass();
                    $act->activity = $activity;
                    $act->details = $this->pm->getActivityDetails($activity);
                    $act->status = $this->pm->getActivityStatus($activity);
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
                if ($activity->getTutor1()) {
                    $this->getStaffReport($activity->getTutor1(), $per);
                }
                if ($activity->getTutor2()) {
                    $this->getStaffReport($activity->getTutor2(), $per);
                }
                if ($activity->getTutor3()) {
                    $this->getStaffReport($activity->getTutor3(), $per);
                }
            }

            $this->report->periods[] = $this->setPeriodStatusLevel($per);
        }

        return $this->report;
    }

    /**
     * @return Collection|null
     */
    public function getCalendarGrades()
    {
        return $this->getTimeTable()->getCalendar()->getCalendarGrades();
    }

    /**
     * @var array
     */
    private $weeks;

    /**
     * @return array
     */
    public function getWeeks(): array
    {
        $this->weeks = $this->weeks ?: [];
        return $this->weeks;
    }

    /**
     * @param \stdClass $week
     * @return TimeTableManager
     */
    public function addWeek(\stdClass $week): TimetableManager
    {
        $this->getWeek();
        // remove none school days
        foreach ($week->days as $q => $day) {
            if (!in_array($day->date->format('D'), $this->schoolWeek))
                unset($week->days[$q]);
        }

        $this->weeks[] = $week;

        return $this;
    }

    /**
     * @return TimeTableManager
     */
    public function clearWeeks(): TimetableManager
    {
        $this->weeks = [];

        return $this;
    }
}