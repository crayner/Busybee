<?php
namespace App\Timetable\Util;

use App\Calendar\Util\CalendarManager;
use App\Core\Util\ReportManager;
use App\Entity\Calendar;
use App\Entity\Space;
use App\Entity\Staff;
use App\Entity\TimetablePeriod;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Connection;

class TimetableReportManager extends ReportManager
{
    /**
     * @return ArrayCollection
     */
    public function getGrades(): ArrayCollection
    {
        return TimetableReportHelper::getGrades();
    }

    /**
     * @return Calendar
     */
    public function getCalendar(): Calendar
    {
        return CalendarManager::getCurrentCalendar();
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
        $report = $report->setEntityManager($this->getEntityManager())->retrieveCache($period) ?: $report;

        $report->addPeriodActivityReports();
        $this->getPeriods()->set($period->getId(), $report);
        return $this;
    }

    /**
     * @param TimetablePeriod $period
     * @return PeriodReportManager
     */
    public function getFullPeriodReport(TimetablePeriod $period): PeriodReportManager
    {
        $this->addPeriod($period);

        return $this->getPeriods()->get($period->getId());
    }

    /**
     * serialize
     *
     * @return string
     */
    public function serialize()
    {
        return serialize([
            $this->getStatus(),
            $this->getMessages(),
            $this->getEntity(),
            $this->periodList,
            $this->getPeriods(),
        ]);
    }

    /**
     * unserialize
     *
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        list(
            $status,
            $messages,
            $entity,
            $this->periodList,
            $this->periods,
            ) = unserialize($serialized);

        $this->setStatus($status)->setMessages($messages)->setEntity($entity);
    }
}