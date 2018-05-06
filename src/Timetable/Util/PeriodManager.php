<?php
namespace App\Timetable\Util;

use App\Calendar\Util\CalendarManager;
use App\Core\Manager\MessageManager;
use App\Core\Manager\SettingManager;
use App\Entity\Calendar;
use App\Entity\CalendarGrade;
use App\Entity\FaceToFace;
use App\Entity\Line;
use App\Entity\Space;
use App\Entity\Staff;
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
     * @var SettingManager
     */
    private $settingManager;

    /**
     * PeriodManager constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, RequestStack $stack, CalendarManager $calendarManager, MessageManager $messageManager, SettingManager $settingManager)
    {
        $this->entityManager = $entityManager;
        $this->stack = $stack;
        $this->calendarManager = $calendarManager;
        $this->messageManager = $messageManager;
        $this->messageManager->setDomain('Timetable');
        $this->settingManager = $settingManager;
    }

    /**
     * Get Period Status
     *
     * @return \stdClass
     */
    public function getPeriodStatus(): PeriodReportManager
    {
        $report = $this->generateFullPeriodReport();

        dump($report);/*
        $status->alert = 'default';
        $status->disableDrop = '';
        $status->id = $this->getPeriod()->getId();

        foreach ($this->getPeriod()->getActivities() as $activity) {
            $report1 = $this->getActivityStatus($activity);
            if (MessageManager::compareLevel($report1->alert, $status->alert))
                $status->alert = $report1->alert;
        }

        if ($report->getMissingStudentCount() > 0) {
            foreach ($report->getMissingStudents()->getIterator() as $q => $students) {
                if (count($students) > 0) {
                    $status->alert = 'danger';
                    $this->getMessageManager()->add($status->alert,'period.students.missing', ['%grade%' => $report->getGrade($q)->getFullName(), 'transChoice' => count($students)], 'Timetable');
                }
            }
        }

        $this->getMessageManager()->add($status->alert,'period.status.messages', ['transChoice' => $this->getMessageManager()->count()], 'Timetable');

        $this->periodStatus = $status; */
        return $report;
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
     * @param $id
     */
    public function getPeriodStudentReport(PeriodReportManager $report)
    {

        $data = new \stdClass();

        $report->setGrades($this->getGrades());

        $this->getPossibleStudents($report);

        $this->getAllocatedStudents($report);

        $report->getMissingStudents();

        return $data;
    }

    /**
     * Is Valid Period
     * @return bool
     */
    public function isValidPeriod($stop = false): bool
    {
        if ($this->getPeriod() instanceof TimetablePeriod && $this->getPeriod()->getId() > 0)
            return true;
        if ($stop)
            throw new \InvalidArgumentException('Dear Programmer: You must set the period in the manager.');

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
            $this->getMessageManager()->add('success', 'period.activities.activity.added', [], 'Timetable');
            $this->getEntityManager()->persist($this->getPeriod());
            $this->getEntityManager()->flush();
        } else
            $this->getMessageManager()->add('warning', 'period.activities.activity.exists', [], 'Timetable');

        $this->clearResults()->getPeriodStatus();

        return;

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
            $data['%{tutors}'] = '';
            $data['%{tutor_ids}'] = '';
            return $data;
        }

        $data = [];
        $data['%{space}'] = is_null($activity->getSpace()) ? '' : $activity->getSpace()->getName();
        $data['%{space_id}'] = is_null($activity->getSpace()) ? '' : $activity->getSpace()->getId();

        $data['%{tutors}'] = '';
        $data['%{tutor_ids}'] = '';
        foreach($activity->getTutors()->getIterator() as $tutor) {
            $data['%{tutors}'] .= $tutor->getFullName() . ', ';
            $data['%{tutor_ids}'] .= $tutor->getFullName() . ', ';
        }
        $data['%{tutors}'] = trim($data['%{tutors}'], ', ');
        $data['%{tutor_ids}'] = trim($data['%{tutor_ids}'], ', ');

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
        $this->clearResults()->getPeriodStatus();
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

    /**
     * @param int $line
     */
    public function addLine(int $line)
    {
        $line = $this->getEntityManager()->getRepository(Line::class)->find($line);

        $count = 0;

        $exists = new ArrayCollection();
        foreach ($this->period->getActivities() as $act)
            $exists->add($act->getActivity());

        foreach ($line->getCourses()->getIterator() as $course)
            foreach($course->getActivities()->getIterator() as $activity)
                if (! $exists->contains($activity)) {
                    $act = new TimetablePeriodActivity();
                    $act->setPeriod($this->period);
                    $act->setActivity($activity);
                    $count++;
                }

        if ($count > 0) {
            $this->getEntityManager()->persist($this->period);
            $this->getEntityManager()->flush();
            $this->getMessageManager()->add('success', 'period.activities.line.added', ['transChoice' => $count], 'Timetable');
            $this->clearResults()->getPeriodStatus();
        } else
            $this->getMessageManager()->add('warning', 'period.activities.line.added', ['transChoice' => 0], 'Timetable');
    }

    /**
     * @param TimetablePeriodActivity $activity
     * @return bool
     */
    public function hasSpace(TimetablePeriodActivity $activity): bool
    {
        if ($activity->loadSpace() instanceof Space)
            return true;
        return false;
    }

    /**
     * @param TimetablePeriodActivity $activity
     * @return Space|null
     */
    public function getSpace(TimetablePeriodActivity $activity): ?Space
    {
        return $activity->loadSpace();
    }

    /**
     * @param TimetablePeriodActivity $activity
     * @return bool
     */
    public function hasTutors(TimetablePeriodActivity $activity): bool
    {
        if ($activity->loadTutors()->count() > 0)
            return true;
        return false;
    }

    /**
     * @param $id
     */
    public function generateFullPeriodReport()
    {
        $this->isValidPeriod(true);
        $report = new PeriodReportManager($this->getPeriod());
        $report->setGrades($this->getGrades())
            ->setActivityReports();
        $this->getPeriodStudentReport($report);

        $types = $this->getSettingManager()->get('space.type.teaching_space');

        $result = $this->getEntityManager()->getRepository(Space::class)->createQueryBuilder('s')
            ->where('s.type in (:types)')
            ->setParameter('types', $types, Connection::PARAM_STR_ARRAY)
            ->orderBy('s.name', 'ASC')
            ->getQuery()
            ->getResult();

        $report->setPossibleSpaces(new ArrayCollection($result));
        $report->setAllocatedSpaces();

        $result = $this->getEntityManager()->getRepository(Staff::class)->findBy([], ['surname' => 'ASC', 'firstName' => 'ASC']);
        $report->setPossibleTutors(new ArrayCollection($result));
        $report->setAllocatedTutors();

        $report->getActivityReportsStatus($this->getGrades());

        return $report;
    }

    /**
     * @return ArrayCollection
     */
    public function getPeriodStaff(): ArrayCollection
    {
        $tutors = new ArrayCollection();
        foreach($this->getPeriod()->getActivities()->getIterator() as $activity)
        {
            foreach($activity->loadTutors()->getIterator() as $tutor)
                if (! $tutors->contains($tutor->getTutor()))
                    $tutors->add($tutor->getTutor());
        }
        return $tutors;
    }

    /**
     * @param PeriodReportManager $data
     * @return bool
     */
    public function hasMissingStudents(PeriodReportManager $data): bool
    {
        return $data->getMissingStudentCount() > 0 ? true : false ;
    }

    /**
     * @param PeriodReportManager $data
     * @return array
     */
    public function getMissingStudents(PeriodReportManager $data): array
    {
        return $data->getMissingStudents()->toArray() ?: [] ;
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

        $result = $this->getEntityManager()->getRepository(CalendarGrade::class)->createQueryBuilder('g')
            ->where('g.calendar = :calendar')
            ->setParameter('calendar', $this->getCurrentCalendar())
            ->select('g')
            ->andWhere('g.grade in (:grades)')
            ->setParameter('grades', $grades, Connection::PARAM_STR_ARRAY)
            ->orderBy('g.sequence', 'ASC')
            ->getQuery()
            ->getResult();
        $this->grades = [];
        foreach($result as $grade)
        {
            $this->grades[$grade->getId()] = $grade;
        }
dump([$this->grades]);

        return $this->grades;
    }

    /**
     * @param PeriodReportManager $report
     */
    private function getPossibleStudents(PeriodReportManager $report)
    {
        //Generate all available students.
        foreach ($report->getGrades() as $grade) {
            $report->addPossibleStudentGrade($grade);
        }
    }

    /**
     * @param PeriodReportManager $report
     */
    private function getAllocatedStudents(PeriodReportManager $report)
    {
        // Generate all Students in the period.
        $report->setCurrentCalendar($this->getCurrentCalendar());

        foreach ($this->getPeriod()->getActivities() as $pa)
            $report->addAllocatedStudents($pa->getActivity());
    }

    /**
     * @return SettingManager
     */
    public function getSettingManager(): SettingManager
    {
        return $this->settingManager;
    }
}
