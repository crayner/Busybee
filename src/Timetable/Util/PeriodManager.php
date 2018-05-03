<?php
namespace App\Timetable\Util;

use App\Calendar\Util\CalendarManager;
use App\Core\Manager\MessageManager;
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
     * PeriodManager constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, RequestStack $stack, CalendarManager $calendarManager, MessageManager $messageManager)
    {
        $this->entityManager = $entityManager;
        $this->stack = $stack;
        $this->calendarManager = $calendarManager;
        $this->messageManager = $messageManager;
        $this->messageManager->setDomain('Timetable');
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
        $status->students = $this->getPeriodStudentReport();

        $status->alert = 'default';
        $status->disableDrop = '';
        $status->id = $this->getPeriod()->getId();

        foreach ($this->getPeriod()->getActivities() as $activity) {
            $report = $this->getActivityStatus($activity);
            if ($this->getMessageManager()->compareLevel($report->alert, $status->alert))
                $status->alert = $report->alert;
        }

        if (! in_array($this->getPeriod()->getPeriodType(), $this->getPeriod()->getPeriodTypeList('no students'))) {
            foreach ($status->students->missingStudents as $q => $students) {
                if (count($students) > 0) {
                    $status->alert = 'danger';
                    $this->getMessageManager()->add($status->alert,'period.students.missing', ['%grade%' => $status->students->grades[$q]->getFullName(), 'transChoice' => count($students)], 'Timetable');
                }
            }
        }

        $this->getMessageManager()->add($status->alert,'period.status.messages', ['transChoice' => $this->getMessageManager()->count()], 'Timetable');

        $this->periodStatus = $status;
        return $status;
    }

    /**
     * @param $id
     * @return TimetablePeriod
     */
    public function find($id): TimetablePeriod
    {
        $this->clearResults();
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
     * @var ArrayCollection
     */
    private $students;

    /**
     * Clear Results
     *
     * @return PeriodManager
     */
    public function clearResults(): PeriodManager
    {
        $this->failedStatus = [];
        $this->spaces = [];
        $this->staff = [];
        $this->students = new ArrayCollection();
        $this->periodStatus = new \stdClass();
        return $this;
    }

    /**
     * @param $id
     */
    public function getPeriodStudentReport()
    {
        if (! $this->isValidPeriod())
            throw new \InvalidArgumentException('Dear Programmer: You must set the period in the manager.');

        $data = new \stdClass();

        $this->grades = $this->getGrades();

        $this->students = new ArrayCollection();
        $students = new ArrayCollection();

        $grades = [];
        //Generate all available students.
        foreach ($this->getGrades() as $grade) {
            $data->grades[$grade->getId()] = $grade;
            $students = new ArrayCollection();
            foreach ($grade->getStudents() as $student) {
                if (! $students->contains($student))
                $students->set($student->getId(), $student);
            }
            $grades[$grade->getId()] = $students;
        }
        $data->availableStudents = $grades;
dump($grades);
        // Generate all Students in the period.
        foreach ($this->getPeriod()->getActivities() as $pa) {
            $act = $pa->getActivity();
            foreach ($act->getStudents() as $student) {
                $this->addStudent($student->getStudent());
                $grade = $student->getStudent()->getStudentCurrentGrade($this->getCurrentCalendar());
                if ($grade instanceof CalendarGrade && isset($grades[$grade->getId()]))
                    $grades[$grade->getId()]->removeElement($student->getStudent());
                elseif ($grade instanceof CalendarGrade && isset($grades[$grade->getId()])){
                    dump([$student,$grade]);die();
                }
            }
        }
dump($this->getStudents());
        foreach ($grades as $q => $grade) {
            if (!empty($grade)) {
                $iterator = $grade->getIterator();
                $iterator->uasort(function ($a, $b) {
                    return ($a->fullName(['surnameFirst' => true, 'preferredOnly' => false]) < $b->fullName(['surnameFirst' => true, 'preferredOnly' => false])) ? -1 : 1;
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

        $this->grades = $this->getEntityManager()->getRepository(CalendarGrade::class)->createQueryBuilder('g')
            ->where('g.calendar = :calendar')
            ->setParameter('calendar', $this->getCurrentCalendar())
            ->select('g')
            ->andWhere('g.grade in (:grades)')
            ->setParameter('grades', $grades, Connection::PARAM_STR_ARRAY)
            ->orderBy('g.sequence', 'ASC')
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
            $this->getMessageManager()->add('success', 'period.activities.activity.added', [], 'Timetable');
            $this->getEntityManager()->persist($this->getPeriod());
            $this->getEntityManager()->flush();
        } else
            $this->getMessageManager()->add('warning', 'period.activities.activity.exists', [], 'Timetable');

        $this->clearResults()->getPeriodStatus();

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
        $this->status->class = ' alert-default';

        if (! $activity->loadSpace() instanceof Space) {
            $this->status->class = ' alert-warning';
            $this->status->alert = 'warning';
            $this->getMessageManager()->add('warning', 'period.activities.activity.space.missing', ['%{name}' => $activity->getFullName()], 'Timetable');
        } else {
            if (isset($this->spaces[$activity->loadSpace()->getName()])) {
                $act = $this->spaces[$activity->loadSpace()->getName()];
                $this->status->class = ' alert-warning';
                $this->status->alert = 'warning';
                $this->getMessageManager()->add('warning','period.activities.activity.space.duplicate', ['%{space}' => $activity->loadSpace()->getName(), '%{activity}' => $activity->getFullName(), '%{activity2}' => $act->getFullName()], 'Timetable');
            }
            $this->spaces[$activity->loadSpace()->getName()] = $activity->getActivity();
        }

        if ($this->hasTutors($activity))
        {
            foreach($activity->loadTutors()->getIterator() as $tutor)
            {
                $id = $tutor->getTutor()->getId();
                if (isset($this->staff[$id]))
                {
                    $act = $this->staff[$id];
                    $this->status->class = ' alert-warning';
                    $this->status->alert = 'warning';
                    $this->getMessageManager()->add('warning','period.activities.activity.staff.duplicate', ['%{name}' => $tutor->getFullName(), '%{activity}' => $activity->getFullName(), '%{activity2}' => $act->getFullName()], 'Timetable');
                }
                else
                    $this->staff[$id] = $activity->getActivity();
            }
        }
        else
        {
            $this->status->class = ' alert-warning';
            $this->status->alert = 'warning';
            $this->getMessageManager()->add('warning','period.activities.activity.staff.missing', ['%{name}' => $activity->getFullName()], 'Timetable');
        }

        if (count($this->getMessageManager()->getMessages()) > 0) {
            $this->failedStatus[$this->status->id] = $this->status->alert = $this->getMessageManager()->getHighestLevel();
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
        $data = $this->getPeriodStudentReport();

        $result = $this->getEntityManager()->getRepository(Space::class)->findBy([], ['name' => 'ASC']);

        $spaces = new ArrayCollection($result);

        $data->spaces = $this->removeUsedSpaces($spaces);

        $result = $this->getEntityManager()->getRepository(Staff::class)->findBy([], ['surname' => 'ASC', 'firstName' => 'ASC']);

        $staff = new ArrayCollection($result);

        $data->staff = $this->removeUsedStaff($staff);

        return $data;
    }

    /**
     * Remove Used Spaces
     *
     * @param $spaces
     * @return mixed
     */
    private function removeUsedSpaces(ArrayCollection $spaces)
    {
        foreach ($this->getPeriodSpaces()->getIterator() as $space)
            if ($spaces->contains($space))
                $spaces->removeElement($space);

        return $spaces;
    }

    /**
     * Remove Used Staff
     *
     * @param ArrayCollection $staff
     * @return ArrayCollection
     */
    private function removeUsedStaff(ArrayCollection $staff): ArrayCollection
    {
        foreach ($this->getPeriodStaff() as $member)
            if ($staff->contains($member))
                $staff->removeElement($member);

        return $staff;
    }

    /**
     * @return ArrayCollection
     */
    public function getPeriodSpaces(): ArrayCollection
    {
        $spaces = new ArrayCollection();

        foreach($this->getPeriod()->getActivities()->getIterator() as $activity)
            if($activity->loadSpace() && !$spaces->contains($activity->loadSpace()))
                $spaces->add($activity->loadSpace());

        return $spaces;
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
     * @return bool
     */
    public function hasMissingStudents(\stdClass $data): bool
    {
        if (empty($data->missingStudents))
            return false;

        foreach($data->missingStudents as $students)
        {
            if (! empty($students))
                return true;
        }
        return false;
    }

    /**
     * @return array
     */
    public function getMissingStudents(\stdClass $data): array
    {
        foreach($data->missingStudents as $gk=>$students){
            if (! empty($students))
                $data->missingStudents[$gk] = array_merge(['grade' => $data->grades[$gk]], $students);
            else
                unset($data->missingStudents[$gk]);
        }

        return $data->missingStudents ?: [] ;
    }

    /**
     * @return ArrayCollection
     */
    public function getStudents(): ArrayCollection
    {
        if (empty($this->students))
            $this->students = [];

        return $this->students;
    }

    public function addStudent(?Student $student): PeriodManager
    {
        if (empty($student))
            return $this;

        $grade = $student->getStudentCurrentGrade($this->getCurrentCalendar());

        if (empty($this->students[$grade->getId()]))
            $this->students[$grade->getId()] = new ArrayCollection();

        if ($this->students[$grade->getId()]->contains($student))
            return $this;

        $this->students[$grade->getId()]->set($student->getId(), $student);

        return $this;
    }
}
