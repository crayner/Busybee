<?php
namespace App\Timetable\Util;

use App\Core\Util\ReportManager;
use App\Entity\Student;
use App\Entity\TimetableLine;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Connection;

class LineReportManager extends ReportManager
{
    /**
     * @var LineManager
     */
    private $lineManager;

    /**
     * @return TimetableLine
     */
    public function getLine(): TimetableLine
    {
        if (empty($this->lineManager))
            throw new \InvalidArgumentException('Inject the line before calling the report.');
        return $this->getLineManager()->getLine();
    }

    /**
     * @return LineReportManager
     */
    public function generateReport(): LineReportManager
    {
        $this->getPossibleStudents();
        $this->getParticipatingStudents();
        return $this;
    }

    /**
     * @return LineManager
     */
    public function getLineManager(): LineManager
    {
        return $this->lineManager;
    }

    /**
     * @param LineManager $lineManager
     * @return LineReportManager
     */
    public function setLineManager(LineManager $lineManager): LineReportManager
    {
        $this->lineManager = $lineManager;
        return $this;
    }

    /**
     * @var ArrayCollection
     */
    private $grades;

    /**
     * @return ArrayCollection
     */
    private function getGrades(): ArrayCollection
    {
        if (! empty($this->grades))
            return $this->grades;

        $this->grades = new ArrayCollection();

        foreach ($this->getLine()->getCourses()->getIterator() as $course) {
            foreach($course->getActivities()->getIterator() as $activity)
                foreach ($activity->getCalendarGrades()->getIterator() as $grade)
                    if (! $this->grades->contains($grade))
                        $this->grades->add($grade);
        }

        return $this->grades;
    }

    /**
     * @var ArrayCollection
     */
    private $possibleStudents;

    /**
     * @return ArrayCollection
     */
    private function getPossibleStudents(): ArrayCollection
    {
        if ($this->possibleStudents)
            return $this->possibleStudents;

        $this->possibleStudents = new ArrayCollection();

        $stu = new Student();

        foreach ($this->getGrades()->getIterator() as $grade) {
            $students = $this->getLineManager()->getEntityManager()->getRepository(Student::class)->createQueryBuilder('s')
                ->leftJoin('s.calendarGrades', 'scg')
                ->leftJoin('scg.calendarGrade', 'cg')
                ->leftJoin('cg.calendar', 'c')
                ->where('cg = :grade')
                ->andWhere('c = :calendar')
                ->andWhere('s.status IN (:statusList)')
                ->setParameter('grade', $grade)
                ->setParameter('calendar', $this->getLine()->getCalendar())
                ->setParameter('statusList', $stu->getStatusList('active'), Connection::PARAM_STR_ARRAY)
                ->orderBy('s.surname', 'ASC')
                ->addOrderBy('s.firstName', 'ASC')
                ->getQuery()
                ->getResult();
            foreach ($students as $student)
                if (!$this->possibleStudents->contains($student))
                    $this->possibleStudents->add($student);
        }

        return $this->possibleStudents;
    }

    /**
     * @var ArrayCollection
     */
    private $participatingStudents;

    /**
     * @var ArrayCollection
     */
    private $duplicatedStudents;

    /**
     * @return lineManager
     */
    private function getParticipatingStudents(): ArrayCollection
    {
        if (! empty($this->participatingStudents))
            return $this->participatingStudents;

        $this->participatingStudents = new ArrayCollection();
        $this->duplicatedStudents = new ArrayCollection();

        foreach ($this->getLine()->getCourses()->getIterator() as $course)
            foreach($course->getActivities()->getIterator() as $activity)
                foreach ($activity->getStudents()->getIterator() as $studentActivity) {
                    $student = $studentActivity->getStudent();
                    if (!$this->participatingStudents->contains($student))
                        $this->participatingStudents->add($student);
                    else
                        if (!$this->duplicatedStudents->contains($student))
                            $this->duplicatedStudents->add($student);
                }

        return $this->participatingStudents;
    }

    /**
     * @return LineReportManager
     */
    public function writeReport(): LineReportManager
    {
        $this->addMessage('primary','line.report.header',
            [
                'useRaw' => true,
                '%learninggroup%' => $this->getLine()->getName(),
                '%participantCount%' => $this->getParticipatingCount(),
                '%possibleCount%' => $this->getPossibleCount(),
            ]
        );

        if ($this->getMissingCount() > 0) {
            $this->addMessage('warning', 'line.report.include_all',
                [
                    'useRaw' => true,
                    'transChoice' => $this->getMissingCount(),
                    'closeButton' => [
                        'mergeClass' => 'btn-sm',
                    ],
                    'resetButton' => [
                        'windowOpen' => [
                            'route' => 'line_test',
                            'route_params' => [
                                'id' => $this->getLine()->getId(),
                            ],
                        ],
                        'title' => 'line.report.refresh.button',
                        'transDomain' => 'Timetable',
                        'mergeClass' => 'btn-sm',
                    ],
                ]
            );

            foreach ($this->getMissingStudents()->getIterator() as $student) {
                $data = [];
                $data['%name%'] = $student->getFullName();
                $data['%identifier%'] = $student->getPerson()->getIdentifier();
                $this->addMessage('dark', 'line.report.student.missing', $data);
            }
        }

        if ($this->getDuplicatedCount() > 0) {
            $this->addMessage('danger', 'line.report.duplicated',
                [
                    'useRaw' => true,
                    'transChoice' => $this->getDuplicatedCount(),
                    'closeButton' => [
                        'mergeClass' => 'btn-sm',
                    ],
                    'resetButton' => [
                        'windowOpen' => [
                            'route' => 'line_test',
                            'route_params' => [
                                'id' => $this->getLine()->getId(),
                            ],
                        ],
                        'title' => 'line.report.refresh.button',
                        'transDomain' => 'Timetable',
                        'mergeClass' => 'btn-sm',
                    ],
                ]
            );

            foreach ($this->getDuplicatedStudents() as $student) {
                $data = [];
                $data['%name%'] = $student->formatName();
                $data['%identifier%'] = $student->getPerson()->getIdentifier();
                $this->addMessage('dark','line.report.student.duplicated', $data);
            }
        }

        if ($this->getExtraCount() > 0) {
            $this->addMessage('danger','line.report.extra',
                [
                    'useRaw' => true,
                    'transChoice' => $this->getExtraCount(),
                    'closeButton' => [
                        'mergeClass' => 'btn-sm',
                    ],
                    'resetButton' => [
                        'windowOpen' => [
                            'route' => 'line_test',
                            'route_params' => [
                                'id' => $this->getLine()->getId(),
                            ],
                        ],
                        'title' => 'line.report.refresh.button',
                        'transDomain' => 'Timetable',
                        'mergeClass' => 'btn-sm',
                    ],
                ]
            );

            foreach ($this->getExtraStudents() as $student) {
                    $data = [];
                    $data['%name%'] = $student->formatName();
                    $data['%identifier%'] = $student->getPerson()->getIdentifier();
                    $this->addMessage('info','line.report.student.extra', $data);
            }
        }


        return $this;
    }

    /**
     * @return int
     */
    public function getPossibleCount(): int
    {
        return $this->getPossibleStudents()->count();
    }

    /**
     * @return int
     */
    public function getParticipatingCount(): int
    {
        return $this->getParticipatingStudents()->count();
    }

    /**
     * @var ArrayCollection
     */
    private $missingStudents;

    /**
     * @return ArrayCollection
     */
    public function getMissingStudents(): ArrayCollection
    {
        if (! empty($this->missingStudents))
            return $this->missingStudents;

        $this->missingStudents = new ArrayCollection();
        foreach($this->getPossibleStudents()->getIterator() as $student)
        {
            if ($this->getParticipatingStudents()->contains($student))
                continue;
            if ($this->missingStudents->contains($student))
                continue;
            $this->missingStudents->add($student);
        }

        return $this->missingStudents;
    }

    /**
     * @return int
     */
    public function getMissingCount(): int
    {
        return $this->getMissingStudents()->count();
    }

    /**
     * @return int
     */
    public function getDuplicatedCount(): int
    {
        return $this->getDuplicatedStudents()->count();
    }

    /**
     * @return ArrayCollection
     */
    public function getDuplicatedStudents(): ArrayCollection
    {
        if (empty($this->duplicatedStudents))
            $this->duplicatedStudents = new ArrayCollection();
        return $this->duplicatedStudents;
    }

    /**
     * @var ArrayCollection
     */
    private $extraStudents;

    /**
     * @return int
     */
    public function getExtraCount(): int
    {
        return $this->getExtraStudents()->count();
    }

    /**
     * @return ArrayCollection
     */
    public function getExtraStudents(): ArrayCollection
    {
        if (! empty($this->extraStudents))
            return $this->extraStudents;

        $this->extraStudents = new ArrayCollection();

        foreach($this->getParticipatingStudents()->getIterator() as $student)
        {
            if ($this->getPossibleStudents()->contains($student) || $this->extraStudents->contains($student))
                continue;
            $this->extraStudents->add($student);
        }

        return $this->extraStudents;
    }
}