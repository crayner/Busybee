<?php
namespace App\Timetable\Util;

use App\Calendar\Util\CalendarManager;
use App\Core\Manager\MessageManager;
use App\Core\Manager\SettingManager;
use App\Entity\Calendar;
use App\Entity\Course;
use App\Entity\TimetableLine;
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
     * @var null|TimetableLine
     */
    private $line;

    /**
     * @param $id
     * @return TimetableLine
     */
    public function find($id): TimetableLine
    {
        $this->line = $this->getEntityManager()->getRepository(TimetableLine::class)->find($id);
        if (! $this->line instanceof TimetableLine)
            $this->line = new TimetableLine();

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
     * @param null $id
     * @return TimetableLine
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
     * @return TimetableLine|null
     */
    public function getLine(): ?TimetableLine
    {
        return $this->line;
    }

    /**
     * @var bool
     */
    private $studentsGenerated = false;

    /**
     * @var bool
     */
    private $participantGenerated = false;

    /**
     * @var bool
     */
    private $possibleGenerated = false;

    /**
     * Get Report
     *
     * @return LineReportManager
     */
    public function getReport(): LineReportManager
    {
        $report = new LineReportManager();

        $report->setLineManager($this)->generateReport();

        $report->writeReport();

        $this->getMessageManager()->addStatusMessages($report->getMessages(), 'Timetable');

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

    /**
     * @return SettingManager
     */
    public function getSettingManager(): SettingManager
    {
        return $this->settingManager;
    }

}