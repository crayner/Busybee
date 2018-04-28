<?php
namespace App\Timetable\Util;

use App\Calendar\Util\CalendarManager;
use App\Core\Manager\MessageManager;
use App\Entity\Calendar;
use App\Entity\CalendarGrade;
use App\Entity\Student;
use App\Entity\TimetablePeriod;
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
     * @param $id Period ID
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
        foreach ($this->period->getActivities() as $activity) {
            $report = $this->getActivityStatus($activity);
            if ($this->alert[$report->alert] > $this->alert[$status->alert]) {
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

        foreach ($this->period->getActivities() as $q => $pa) {
            $act = $pa->getActivity();
            foreach ($act->getStudents() as $student) {
                $grade = $student->getStudentCalendarGroup($this->currentYear);

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
     * get Grade Control
     * @return array
     */
    private function getGradeControl(): array
    {
        return is_array($this->getStack()->getCurrentRequest()->getSession()->get('gradeControl')) ? $this->getStack()->getCurrentRequest()->getSession()->get('gradeControl') : [];
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
}