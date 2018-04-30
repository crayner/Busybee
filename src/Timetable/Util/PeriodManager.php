<?php
namespace App\Timetable\Util;

use App\Calendar\Util\CalendarManager;
use App\Core\Manager\MessageManager;
use App\Entity\Calendar;
use App\Entity\CalendarGrade;
use App\Entity\FaceToFace;
use App\Entity\Student;
use App\Entity\TimetablePeriod;
use App\Entity\TimetablePeriodActivity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class PeriodManager
{
    /**
     * @var TimetablePeriod
     */
    private $period;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var RequestStack 
     */
    private $stack;

    /**
     * @var CalendarManager
     */
    private $calendarManager;

    /**
     * @var MessageManager
     */
    private $messageManager;

    /**
     * PeriodManager constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, RequestStack $stack, CalendarManager $calendarManager, MessageManager $messageManager)
    {
        $this->entityManager = $entityManager;
        $this->stack = $stack;
        $this->calendarManager = $calendarManager;
        $this->messageManager = $messageManager;
    }

    /**
     * @var \stdClass
     */
    private $periodStatus;

    /**
     * Get Period Status
     *
     * @return \stdClass
     */
    public function getPeriodStatus(): \stdClass
    {
        if (!empty($this->periodStatus->id) && $this->periodStatus->id === $this->getPeriod()->getId())
            return $this->periodStatus;

        $this->clearResults();

        $status = new \stdClass();
        $status->students = $this->generateFullPeriodReport();

        $status->alert = 'default';
        $status->disableDrop = '';
        $status->id = $this->getPeriod()->getId();

        $problems = false;
        foreach ($this->getPeriod()->getActivities() as $activity) {
            $report = $this->getActivityStatus($activity);
            if ($this->getMessageManager()->compareLevel($report->alert, $status->alert)) {
                $status->alert = $report->alert;
                $problems = true;
            }
        }

        if ($problems) {
           $this->getMessageManager()->add($status->alert,'period.activities.problems', [], 'Timetable');
        }

        if (! in_array($this->getPeriod()->getPeriodType(), $this->getPeriod()->getPeriodTypeList('no students'))) {
            foreach ($status->students->missingStudents as $q => $students) {
                if (count($students) > 0) {
                    $status->alert = 'danger';
                    $this->getMessageManager()->add($status->alert,'period.students.missing', ['%grade%' => $status->students->grades[$q]->getFullName()], 'Timetable');
                }
            }
        }

        $this->periodStatus = $status;
        return $status;
    }

    /**
     * @param $id
     * @return TimetablePeriod
     */
    public function find($id): TimetablePeriod
    {
        return $this->setPeriod($this->getEntityManager()->getRepository(TimetablePeriod::class)->find(intval($id)))->getPeriod();
    }

    /**
     * @return null|TimetablePeriod
     */
    public function getPeriod(): ?TimetablePeriod
    {
        return $this->period;
    }

    /**
     * @param TimetablePeriod $period
     * @return PeriodManager
     */
    public function setPeriod(TimetablePeriod $period): PeriodManager
    {
        $this->period = $period;
        return $this;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * @param \stdClass $periodStatus
     * @return PeriodManager
     */
    public function setPeriodStatus(\stdClass $periodStatus): PeriodManager
    {
        $this->periodStatus = $periodStatus;
        return $this;
    }

    /**
     * @var array
     */
    private $failedStatus = [];

    /**
     * @var array
     */
    private $spaces = [];

    /**
     * @var array
     */
    private $staff = [];

    /**
     * @var array
     */
    private $students = [];

    /**
     * Clear Results
     */
    public function clearResults()
    {
        $this->failedStatus = [];
        $this->spaces = [];
        $this->staff = [];
        $this->students = [];
    }

    /**
     * @param $id
     */
    public function generateFullPeriodReport()
    {
        if (! $this->isValidPeriod())
            throw new \InvalidArgumentException('Dear Programmer: You must set the period in the manager.');

        $data = new \stdClass();

        $this->grades = $this->getGrades();

        $grades = [];
        foreach ($this->grades as $grade) {
            $data->grades[$grade->getId()] = $grade;
            $students = [];
            foreach ($grade->getStudents() as $student)
                $students[$student->getId()] = $student;
            $grades[$grade->getId()] = $students;
        }

        foreach ($this->getPeriod()->getActivities() as $q => $pa) {
            $act = $pa->getActivity();
            foreach ($act->getStudents() as $student) {
                $grade = $student->getStudentCalendarGrades($this->getCurrentCalendar());

                if ($grade instanceof Grade && isset($grades[$grade->getId()][$student->getId()]))
                    unset($grades[$grade->getId()][$student->getId()]);
            }
        }

        foreach ($grades as $q => $grade) {
            if (!empty($grade)) {
                $grade = new ArrayCollection($grade);
                $iterator = $grade->getIterator();
                $iterator->uasort(function ($a, $b) {
                    return ($a->formatName(['surnameFirst' => true, 'preferredOnly' => false]) < $b->formatName(['surnameFirst' => true, 'preferredOnly' => false])) ? -1 : 1;
                });
                $grades[$q] = iterator_to_array($iterator, true);
            }
        }


        $data->missingStudents = $grades;

        return $data;
    }

    /**
     * Is Valid Period
     * @return bool
     */
    public function isValidPeriod(): bool
    {
        if ($this->getPeriod() instanceof TimetablePeriod && $this->getPeriod()->getId() > 0)
            return true;
        return false;
    }

    /**
     * @var array
     */
    private $grades = [];

    /**
     * @return array
     */
    private function getGrades()
    {
        if (!empty($this->grades))
            return $this->grades;
        $grades = [];

        foreach ($this->getGradeControl() as $grade => $xxx)
            if ($xxx)
                $grades[] = $grade;

        $stu = new Student();

        $this->grades = $this->getEntityManager()->getRepository(CalendarGrade::class)->createQueryBuilder('g')
            ->leftJoin('g.calendar', 'c')
            ->leftJoin('g.students', 's')
            ->where('c = :calendar')
            ->setParameter('calendar', $this->getCurrentCalendar())
            ->select('g')
            ->addSelect('s')
            ->andWhere('s.status IN (:status)')
            ->setParameter('status', $stu->getStatusList('active'), Connection::PARAM_STR_ARRAY)
            ->andWhere('g.grade in (:grades)')
            ->setParameter('grades', $grades, Connection::PARAM_STR_ARRAY)
            ->getQuery()
            ->getResult();

        return $this->grades;
    }

    /**
     * @return RequestStack
     */
    public function getStack(): RequestStack
    {
        return $this->stack;
    }

    /**
     * @return Calendar
     */
    public function getCurrentCalendar(): Calendar
    {
        return $this->calendarManager->getCurrentCalendar();
    }

    /**
     * @return MessageManager
     */
    public function getMessageManager(): MessageManager
    {
        return $this->messageManager;
    }

    /**
     * @param $activity
     */
    public function addActivity($activity)
    {
        $activity = $this->getEntityManager()->getRepository(FaceToFace::class)->find($activity);

        $exists = new ArrayCollection();
        foreach ($this->getPeriod()->getActivities() as $act)
            $exists->add($act->getActivity());

        if (!$exists->contains($activity)) {
            $act = new TimetablePeriodActivity();
            $act->setPeriod($this->getPeriod());
            $act->setActivity($activity);
            $this->getPeriod()->getActivities()->add($act);
            $this->getMessageManager()->add('success', 'period.activities.line.added', [], 'Timetable');
            $this->getEntityManager()->persist($this->getPeriod());
            $this->getEntityManager()->flush();
        } else
            $this->getMessageManager()->add('warning', 'period.activities.line.none', [], 'Timetable');

        return;

    }

    /**
     * @param TimetablePeriodActivity|null $activity
     * @return \stdClass
     */
    public function getActivityStatus(TimetablePeriodActivity $activity = null): \stdClass
    {
        if (!$activity instanceof TimetablePeriodActivity) {
            $status = new \stdClass();
            $status->class = 'default';
            $status->alert = 'default';
            $status->id = null;
            $this->status = $status;
            return $status;
        }

        if (isset($this->status->id) && $this->status->id === $activity->getId())
            return $this->status;

        $this->status = new \stdClass();
        $this->status->id = $activity->getId();
        $this->status->alert = 'default';
        $this->status->class = '';

        if (is_null($activity->getSpace())) {
            $this->status->class = ' alert-warning';
            $this->status->alert = 'warning';
            $this->getMessageManager()->add('warning', 'period.activities.activity.space.missing', [], 'Timetable');
        } else {
            if (isset($this->spaces[$activity->getSpace()->getName()])) {
                $act = $this->spaces[$activity->getSpace()->getName()];
                $this->status->class = ' alert-warning';
                $this->status->alert = 'warning';
                $this->getMessageManager()->add('warning','period.activities.activity.space.duplicate', ['%space%' => $activity->getSpace()->getName(), '%activity%' => $activity->getFullName(), '%activity2%' => $act->getFullName()], 'Timetable');
            }
            $this->spaces[$activity->getSpace()->getName()] = $activity->getActivity();
        }

        if (is_null($activity->getTutor1())) {
            $this->status->class = ' alert-warning';
            $this->status->alert = 'warning';
            $this->getMessageManager()->add('warning','period.activities.activity.staff.missing', [], 'Timetable');
        } elseif (!is_null($activity->getTutor1()) && isset($this->staff[$activity->getTutor1()->getFullName()])) {
            $act = $this->staff[$activity->getTutor1()->getFullName()];
            $this->status->class = ' alert-warning';
            $this->status->alert = 'warning';
            $this->getMessageManager()->add('warning','period.activities.activity.staff.duplicate', ['%name%' => $activity->getTutor1()->getFullName(), '%activity%' => $activity->getFullName(), '%activity2%' => $act->getFullName()], 'Timetable');
        } elseif (!is_null($activity->getTutor1()) && !isset($this->staff[$activity->getTutor1()->getFullName()]))
            $this->staff[$activity->getTutor1()->getFullName()] = $activity->getActivity();

        if (!is_null($activity->getTutor2()) && isset($this->staff[$activity->getTutor2()->getFullName()])) {
            $act = $this->staff[$activity->getTutor2()->getFullName()];
            $this->status->class = ' alert-warning';
            $this->status->alert = 'warning';
            $this->getMessageManager()->add('warning','period.activities.activity.staff.duplicate', ['%name%' => $activity->getTutor1()->getFullName(), '%activity%' => $activity->getFullName(), '%activity2%' => $act->getFullName()], 'Timetable');
        } elseif (!is_null($activity->getTutor2()) && !isset($this->staff[$activity->getTutor2()->getFullName()]))
            $this->staff[$activity->getTutor2()->getFullName()] = $activity->getActivity();

        if (!is_null($activity->getTutor3()) && isset($this->staff[$activity->getTutor3()->getFullName()])) {
            $act = $this->staff[$activity->getTutor3()->getFullName()];
            $this->status->class = ' alert-warning';
            $this->status->alert = 'warning';
            $this->getMessageManager()->add('warning','period.activities.activity.staff.duplicate', ['%name%' => $activity->getTutor1()->getFullName(), '%activity%' => $activity->getFullName(), '%activity2%' => $act->getFullName()], 'Timetable');
        } elseif (!is_null($activity->getTutor3()) && !isset($this->staff[$activity->getTutor3()->getFullName()]))
            $this->staff[$activity->getTutor3()->getFullName()] = $activity->getActivity();

        if (count($this->getMessageManager()->getMessages()) > 0) {
            $this->failedStatus[$this->status->id] = $this->status->alert = $this->getMessageManager()->getHighestLevel();
            $this->getMessageManager()->add($this->status->alert,'period.activities.activity.report.button', [], 'Timetable');
        }

        return $this->status;
    }

    /**
     * get Grade Control
     * @return array
     */
    public function getGradeControl(): array
    {
        return is_array($this->getStack()->getCurrentRequest()->getSession()->get('gradeControl')) ? $this->getStack()->getCurrentRequest()->getSession()->get('gradeControl') : [];
    }

    /**
     * @param TimetablePeriodActivity|null $activity
     * @return array
     */
    public function getActivityDetails(?TimetablePeriodActivity $activity): array
    {
        if (!$activity instanceof TimetablePeriodActivity) {
            $data = [];
            $data['%space%'] = '';
            $data['%tutor1%'] = '';
            $data['%tutor2%'] = '';
            $data['%tutor3%'] = '';
            return $data;
        }

        $data = [];
        $data['%space%'] = is_null($activity->getSpace()) ? '' : $activity->getSpace()->getName();
        $data['space_id'] = is_null($activity->getSpace()) ? '' : $activity->getSpace()->getId();
        $data['%tutor1%'] = is_null($activity->getTutor1()) ? '' : $activity->getTutor1()->getFullName();
        $data['tutor1_id'] = is_null($activity->getTutor1()) ? '' : $activity->getTutor1()->getId();
        $data['%tutor2%'] = is_null($activity->getTutor2()) ? '' : $activity->getTutor2()->getFullName();
        $data['tutor2_id'] = is_null($activity->getTutor2()) ? '' : $activity->getTutor2()->getId();
        $data['%tutor3%'] = is_null($activity->getTutor3()) ? '' : $activity->getTutor3()->getFullName();
        $data['tutor3_id'] = is_null($activity->getTutor3()) ? '' : $activity->getTutor3()->getId();

        return $data;
    }

    /*
     * @var null|\stdClass
     */
    private $status;

    /**
     * @param int $id
     * @return bool
     */
    public function removeActivity(int $id): bool
    {
        $this->status = new \stdClass();

        $this->status->status = 'success';

        $this->findActivity($id);

        if (! $this->activity)
        {
            $this->status->status = 'warning';
            $this->getMessageManager()->add('warning', 'period.activities.activity.missing.warning', [], 'Timetable');
            return false;
        }

        $this->getPeriod()->removeActivity($this->getActivity());

        try {
            $this->getEntityManager()->persist($this->getPeriod());
            $this->getEntityManager()->remove($this->getActivity());
            $this->getEntityManager()->flush();
        } catch (\Exception $e) {
            $this->getMessageManager()->add('danger', 'period.activities.activity.remove.error', ['%name%' => $this->getActivity()->getFullName(), '%error%' => $e->getMessage()], 'Timetable');
            $this->status->status = 'error';
            return false;
        }
        $this->getMessageManager()->add('success', 'period.activities.activity.remove.success', ['%name%' => $this->getActivity()->getFullName()], 'Timetable');
        return true;
    }

    /**
     * @var TimetablePeriodActivity|null
     */
    private $activity;

    /**
     * @param int $id
     * @return TimetablePeriodActivity|null
     */
    public function findActivity(int $id): ?TimetablePeriodActivity
    {
        return $this->setActivity($this->getEntityManager()->getRepository(TimetablePeriodActivity::class)->find(intval($id)))->getActivity();
    }

    /**
     * @return TimetablePeriodActivity|null
     */
    public function getActivity(): ?TimetablePeriodActivity
    {
        return $this->activity;
    }

    /**
     * @param TimetablePeriodActivity|null $activity
     * @return PeriodManager
     */
    public function setActivity(?TimetablePeriodActivity $activity): PeriodManager
    {
        $this->activity = $activity;
        return $this;
    }

    /**
     * @return null|\stdClass
     */
    public function getStatus(): ?\stdClass
    {
        return $this->status;
    }
}