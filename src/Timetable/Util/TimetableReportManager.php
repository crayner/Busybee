<?php
namespace App\Timetable\Util;

use App\Core\Util\ReportManager;
use App\Entity\Calendar;
use App\Entity\Space;
use App\Entity\Staff;
use App\Entity\TimetablePeriod;
use App\School\Util\SpaceManager;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Connection;

class TimetableReportManager extends ReportManager
{
    /**
     * @var ArrayCollection
     */
    private $grades;

    /**
     * @return ArrayCollection
     */
    public function getGrades(): ArrayCollection
    {
        if (empty($this->grades))
            throw new \InvalidArgumentException('The grades need to be injected into the report.');
        return $this->grades;
    }

    /**
     * @param ArrayCollection $grades
     * @return TimetableReportManager
     */
    public function setGrades(ArrayCollection $grades): TimetableReportManager
    {
        if ($this->grades !== $grades)
            $this->setRefreshReport(true);
        $this->grades = $grades;
        return $this;
    }

    /**
     * @var Calendar
     */
    private $calendar;

    /**
     * @return Calendar
     */
    public function getCalendar(): Calendar
    {
        if (empty($this->calendar))
            throw new \InvalidArgumentException('Injection of the current calendar is required.');
        return $this->calendar;
    }

    /**
     * @param Calendar $calendar
     * @return TimetableReportManager
     */
    public function setCalendar(Calendar $calendar): TimetableReportManager
    {
        $this->calendar = $calendar;
        return $this;
    }

    /**
     * @var ArrayCollection
     */
    private $periodList;

    /**
     * @return TimetableReportManager
     */
    public function setPeriodList(array $results): TimetableReportManager
    {
        $periods = new ArrayCollection();

        foreach($results as $entity)
            $periods->add($entity['entity']);

        if ($periods !== $this->periodList)
            $this->setRefreshReport(true);

        $this->periodList = $periods;

        foreach($this->periodList->getIterator() as $item)
            $this->addPeriod($item);

        return $this;
    }
    /**
     * @var ArrayCollection|null
     */
    private $periods;

    /**
     * @return ArrayCollection
     */
    public function getPeriods(): ArrayCollection
    {
        if (empty($this->periods))
            $this->periods = new ArrayCollection();
        return $this->periods;
    }

    /**
     * @param TimetablePeriod $period
     * @return TimetableReportManager
     */
    public function addPeriod(TimetablePeriod $period): TimetableReportManager
    {
        $report = new PeriodReportManager();
        $report = $report->setEntityManager($this->getEntityManager())->retrieveCache($period, TimetablePeriod::class);

        $spaces = $this->getEntityManager()->getRepository(Space::class)->createQueryBuilder('s')
            ->where('s.type in (:types)')
            ->setParameter('types', $this->getSpaceTypes(), Connection::PARAM_STR_ARRAY)
            ->orderBy('s.name', 'ASC')
            ->getQuery()
            ->getResult();
        $spaces = new ArrayCollection($spaces);

        $tutors = $this->getEntityManager()->getRepository(Staff::class)->findBy([], ['surname' => 'ASC', 'firstName' => 'ASC']);
        $tutors = new ArrayCollection($tutors);

        $report
            ->setGrades($this->getGrades())
            ->setCalendar($this->getCalendar())
            ->generateActivityReports()
            ->addPossibleStudents()
            ->addAllocatedStudents()
            ->setMissingStudents()
            ->setPossibleSpaces($spaces)
            ->setAllocatedSpaces()
            ->setPossibleTutors($tutors)
            ->setAllocatedTutors()
            ->writeReport()
        ;
        $this->getPeriods()->set($period->getId(), $report);
        return $this;
    }

    /**
     * @var array
     */
    private $spaceTypes;

    /**
     * @return array
     */
    public function getSpaceTypes(): array
    {
        return $this->spaceTypes;
    }

    /**
     * @param array $spaceTypes
     * @return TimetableReportManager
     */
    public function setSpaceTypes(array $spaceTypes): TimetableReportManager
    {
        $this->spaceTypes = $spaceTypes;
        return $this;
    }
}