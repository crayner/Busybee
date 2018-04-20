<?php
namespace App\Timetable\Util;

use App\Calendar\Util\CalendarManager;
use App\Core\Exception\Exception;
use App\Core\Manager\MessageManager;
use App\Entity\Calendar;
use App\Entity\Course;
use App\Entity\Line;
use App\Entity\Student;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;

class LineManager
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * Current Calednar
     * @var Calendar
     */
    private $calendar;

    /**
     * @var MessageManager
     */
    private $messageManager;

    /**
     * LineManager constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, CalendarManager $calendarManager)
    {
        $this->entityManager = $entityManager;
        $this->setCalendar($calendarManager->getCurrentCalendar());
        $this->messageManager = $calendarManager->getMessageManager();
        $this->messageManager->setDomain('Timetable');
    }

    /**
     * @var null|Line
     */
    private $line;

    /**
     * @param $id
     * @return Line
     */
    public function find($id): Line
    {
        $this->line = $this->getEntityManager()->getRepository(Line::class)->find($id);
        if (! $this->line instanceof Line)
            $this->line = new Line();

        $this->line->setCalendar($this->calendar);
        return $this->line;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * @return Calendar
     */
    public function getCalendar(): Calendar
    {
        return $this->calendar;
    }

    /**
     * @param Calendar $calendar
     * @return LineManager
     */
    public function setCalendar(Calendar $calendar): LineManager
    {
        $this->calendar = $calendar;
        return $this;
    }

    /**
     * @return MessageManager
     */
    public function getMessageManager(): MessageManager
    {
        return $this->messageManager;
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
     * @return LineManager
     */
    public function setStatus(string $status): LineManager
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @param $id
     */
    public function removeCourse($id)
    {
        if (intval($id) == 0)
            return ;

        $course = $this->getEntityManager()->getRepository(Course::class)->find($id);

        if ($course instanceof Course)
        {
            $this->line->removeCourse($course);

            $this->getEntityManager()->persist($course);
            $this->getEntityManager()->flush();
            $this->getMessageManager()->add('success', 'line.course.remove.success', ['%{name}' => $course->getName()]);
            return ;
        }

        $this->getMessageManager()->add('warning', 'line.course.remove.missing', []);
    }

    /**
     * @param integer $id
     */
    public function generateReport($id): LineManager
    {
        $this->addLine($id);

        $this->generateGrades()
            ->generateStudentList()
            ->generateParticipantList()
            ->generatePossibleList();

        return $this;
    }

    /**
     * @param null $id
     * @return line
     */
    public function addLine($id = null)
    {
        if (is_null($id))
            return $this->line;

        $this->line = $this->find($id);
        $this->gradesGenerated = false;
        $this->studentsGenerated = false;
        $this->participantGenerated = false;
        $this->possibleGenerated = false;

        return $this->getLine();
    }

    /**
     * @return Line|null
     */
    public function getLine(): ?Line
    {
        return $this->line;
    }

    /**
     * @var boolean
     */
    private $gradesGenerated = false;

    /**
     * @return lineManager
     */
    private function generateGrades()
    {
        if ($this->gradesGenerated)
            return $this;

        $this->grades = new ArrayCollection();

        foreach ($this->getLine()->getCourses()->getIterator() as $course) {
            foreach($course->getActivities()->getIterator() as $activity)
                foreach ($activity->getCalendarGrades()->getIterator() as $grade)
                    if (! $this->grades->contains($grade))
                        $this->grades->add($grade);
        }

        $this->gradesGenerated = true;
        return $this;
    }

    /**
     * @var bool
     */
    private $studentsGenerated = false;

    /**
     * @var Collection
     */
    private $grades;

    /**
     * @var Collection
     */
    private $students;

    /**
     * @return lineManager
     */
    private function generateStudentList()
    {
        if ($this->studentsGenerated)
            return $this;

        $this->students = new ArrayCollection();

        foreach ($this->grades->getIterator() as $grade) {
            $students = $this->getEntityManager()->getRepository(Student::class)->createQueryBuilder('s')
                ->leftJoin('s.calendarGrades', 'cg')
                ->leftJoin('cg.calendar', 'c')
                ->where('cg.id = :grade_id')
                ->andWhere('c.id = :calendar_id')
                ->andWhere('s.status IN (:statusList)')
                ->setParameter('grade_id', $grade->getId())
                ->setParameter('calendar_id', $this->line->getCalendar()->getId())
                ->setParameter('statusList', ['current', 'future'], Connection::PARAM_STR_ARRAY)
                ->getQuery()
                ->getResult();
            foreach ($students as $student)
                if (!$this->students->contains($student))
                    $this->students->add($student);
        }

        $this->studentsGenerated = true;
        return $this;
    }

    /**
     * @var bool
     */
    private $participantGenerated = false;

    /**
     * @var Collection
     */
    private $participant;

    /**
     * @var Collection
     */
    private $duplicated;

    /**
     * @return lineManager
     */
    private function generateParticipantList()
    {
        if ($this->participantGenerated)
            return $this;

        $this->participant = new ArrayCollection();
        $this->duplicated = new ArrayCollection();

        foreach ($this->line->getCourses()->getIterator() as $course)
            foreach($course->getActivities()->getIterator() as $activity)
                foreach ($activity->getStudents()->getIterator() as $studentActivity) {
                    $student = $studentActivity->getStudent();
                    $student->addActivityToList($activity);
                    if (!$this->participant->contains($student))
                        $this->participant->add($student);
                    else
                        if (!$this->duplicated->contains($student))
                            $this->duplicated->add($student);
                }
        $this->participantGenerated = true;
        return $this;
    }

    /**
     * @var bool
     */
    private $possibleGenerated = false;

    /**
     * @var Collection
     */
    private $possible;

    /**
     * @return lineManager
     */
    private function generatePossibleList()
    {
        if ($this->possibleGenerated)
            return $this;

        $this->possible = new ArrayCollection();

        foreach ($this->students->getIterator() as $student)
            if (!$this->participant->contains($student))
                $this->possible->add($student);

        $this->possibleGenerated = true;
        return $this;
    }

    /**
     * Get Report
     *
     * @return string|html
     */
    public function getReport()
    {
        $report = [];
        $report['%learninggroup%'] = $this->line->getName();
        $report['%possibleCount%'] = $this->possibleCount = $this->getPossibleCount();
        $report['%studentCount%'] = $this->studentCount = $this->getStudentCount();
        $report['%duplicateCount%'] = $this->duplicateCount = $this->getDuplicateCount();
        $report['%participantCount%'] = $this->participantCount = $this->getParticipantCount();
        $report['%includeAll%'] = $this->getIncludeAll();
        $report['%missingStudents%'] = $this->getMissingStudents();
        $report['%exceededMax%'] = $this->getExceededMax();
        $report['%allowed%'] = $this->getPossibleCount();


        $this->getMessageManager()->add('primary','line.report.header', $report);

        if (!$this->getIncludeAll()) {
            $this->getMessageManager()->add('warning','line.report.includeAll', $report);

            $iterator = $this->possible->getIterator();
            $iterator->uasort(function ($a, $b) {
                return ($a->getPerson()->getSurname() < $b->getPerson()->getSurname()) ? -1 : 1;
            });
            $this->possible = new ArrayCollection(iterator_to_array($iterator, false));

            foreach ($this->possible as $student) {
                $data = [];
                $data['%name%'] = $student->formatName();
                $data['%identifier%'] = $student->getPerson()->getIdentifier();
                $data['%activityList%'] = $student->getActivityList();
                $this->getMessageManager()->add('warning','line.report.student', $data);
            }
        }

        if ($this->getExceededMax())
            $this->getMessageManager()->add('danger','line.report.exceededMax', $report);

        if ($this->getDuplicateCount() > 0) {
            $this->getMessageManager()->add('danger', 'line.report.duplicated', $report);

            $iterator = $this->duplicated->getIterator();
            $iterator->uasort(function ($a, $b) {
                return ($a->getPerson()->getSurname() < $b->getPerson()->getSurname()) ? -1 : 1;
            });
            $this->duplicated = new ArrayCollection(iterator_to_array($iterator, false));

            foreach ($this->duplicated as $student) {
                $data = [];
                $data['%name%'] = $student->formatName();
                $data['%identifier%'] = $student->getPerson()->getIdentifier();
                $data['%activityList%'] = $student->activityList;
                $this->getMessageManager()->add('warning','line.report.student', $data);
            }
        }

        if ($this->participantCount > $this->studentCount) {
            $this->getMessageManager()->add('danger','line.report.extra', $report);

            $iterator = $this->participant->getIterator();
            $iterator->uasort(function ($a, $b) {
                return ($a->getPerson()->getSurname() < $b->getPerson()->getSurname()) ? -1 : 1;
            });
            $this->participant = new ArrayCollection(iterator_to_array($iterator, false));

            foreach ($this->participant as $student) {
                if (!$this->students->contains($student)) {
                    $data = [];
                    $data['%name%'] = $student->formatName();
                    $data['%identifier%'] = $student->getPerson()->getIdentifier();
                    $data['%activityList%'] = $student->activityList;
                    $this->getMessageManager()->add('info','line.report.student', $data);
                }
            }
        }

        $this->getMessageManager()->add('primary','line.report.footer', $report);

        return $report;
    }

    /**
     * @var int
     */
    private $possibleCount = 0;

    /**
     * @return int
     */
    public function getPossibleCount(): int
    {
        $this->possibleCount = $this->possible->count();
        return $this->possibleCount;
    }

    /**
     * @var int
     */
    private $studentCount = 0;

    /**
     * @return int
     */
    public function getStudentCount(): int
    {
        $this->studentCount = $this->students->count();
        return $this->studentCount;
    }

    /**
     * @var int
     */
    private $duplicateCount = 0;

    /**
     * @return int
     */
    public function getDuplicateCount(): int
    {
        $this->duplicateCount = $this->duplicated->count();
        return $this->duplicateCount;
    }

    /**
     * @var int
     */
    private $participantCount = 0;

    /**
     * @return int
     */
    public function getParticipantCount(): int
    {
        $this->participantCount = $this->participant->count();
        return $this->participantCount;
    }

    /**
     * @return bool
     */
    public function getIncludeAll(): bool
    {
        if ($this->getPossibleCount() > 0)
            return false;

        return $true;
    }

    /**
     * @var Collection
     */
    private $missingStudents;

    /**
     * @return Collection
     */
    public function getMissingStudents(): Collection
    {
        $this->missingStudents = new ArrayCollection();

        if ($this->getPossibleCount() == 0)
            return $this->missingStudents;

        foreach ($this->possible->getIterator() as $student)
            if ($student instanceof Student && ! $this->missingStudents->contains($student))
                $this->missingStudents->add($student);

        return $this->missingStudents;
    }

    /**
     * @var bool
     */
    private $exceededMax = false;

    /**
     * @return bool
     */
    public function getExceededMax()
    {
        $this->exceededMax = false;

        // Test OK if includeAll not set
        if ($this->getParticipantCount() > $this->getStudentCount())
            $this->exceededMax = true;
        dump($this);

        return $this->exceededMax;
    }

}