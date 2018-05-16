<?php
namespace App\Core\Util;

use App\Core\Manager\MessageManager;
use App\Entity\ReportCache;
use Doctrine\ORM\EntityManagerInterface;

abstract class ReportManager implements ReportInterface
{
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
     * @return ReportManager
     */
    public function setStatus(string $status): ReportManager
    {
        if (! in_array($status, MessageManager::$statusLevel) || $this->status === 'danger')
            return $this;
        if (MessageManager::compareLevel($status, $this->status))
            $this->status = $status;
        return $this;
    }

    /**
     * @var array
     */
    private $messages;

    /**
     * @return array
     */
    public function getMessages(): array
    {
        if (empty($this->messages))
            $this->messages = [];
        return $this->messages;
    }

    public function clearMessages(): ReportManager
    {
        $this->messages = [];
        return $this;
    }

    /**
     * @param string $level
     * @param string $message
     * @param array $options
     * @return ReportManager
     */
    public function addMessage(string $level, string $message, array $options = []): ReportManager
    {
        $mess = new \stdClass();
        $mess->level = $level;
        $mess->message = $message;
        $mess->options = $options;
        $this->messages = $this->setStatus($level)
            ->getMessages();
        $this->messages[] = $mess;
        return $this;
    }

    /**
     * @var object|null
     */
    private $entity;

    /**
     * @var bool
     */
    private $refreshReport;

    /**
     * retrieveCache
     *
     * @param $entity
     * @return ReportInterface
     */
    public function retrieveCache($entity): ReportInterface
    {
        $em = $this->getEntityManager();
        $report = $this->loadReport($entity);
        if ($report instanceof ReportCache) {
            $lastModified = $report->getLastModified();
            $report = $report->getReport();
            $report->setRefreshReport(! $entity->isEqualTo($report->getEntity()));
            if ($lastModified < new \DateTime("-15 minutes"))
                $report->setRefreshReport(true);
            if ($report->isRefreshReport())
                $report->setEntity($entity);
            $report->setEntityManager($em);
            return $report;
        }
        $this->refreshReport = true;
        $this->setEntity($entity);
        $this->setEntityManager($em);
        return $this;
    }

    /**
     * @return ReportInterface
     */
    public function getReport(): ReportInterface
    {
        return $this->report;
    }

    /**
     * @var EntityManagerInterface|null
     */
    private $entityManager;

    /**
     * @return EntityManagerInterface|null
     */
    public function getEntityManager(): ?EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * @param EntityManagerInterface|null $entityManager
     * @return ReportManager
     */
    public function setEntityManager(?EntityManagerInterface $entityManager): ReportManager
    {
        $this->entityManager = $entityManager;
        return $this;
    }

    /**
     * @return null|object
     */
    public function getEntity(): ?object
    {
        return $this->entity;
    }

    /**
     * @param null|object $entity
     * @return ReportManager
     */
    public function setEntity(?object $entity): ReportManager
    {
        $this->entity = $entity;
        return $this;
    }

    /**
     * @return bool
     */
    public function isRefreshReport(): bool
    {
        return $this->refreshReport ? true : false ;
    }

    /**
     * @param bool $refreshReport
     * @return ReportManager
     */
    public function setRefreshReport(bool $refreshReport): ReportManager
    {
        $this->refreshReport = $refreshReport;
        return $this;
    }

    /**
     * @var integer|null
     */
    private $reportId;

    /**
     * @return ReportManager
     */
    public function saveReport(): ReportManager
    {
        $report = $this->loadReport();
        if (! $report)
            $report = new ReportCache();

        $report->setClassName(get_class($this->getEntity()));
        $report->setClassId($this->getEntity()->getId());
        $em = $this->getEntityManager();
        $this->setEntityManager(null);
        $report->setReport($this);
        $em->persist($report);
        $em->flush();
        $this->setEntityManager($em);
        return $this;
    }

    /**
     * @param null $entity
     * @return ReportCache|null
     */
    private function loadReport($entity = null): ?ReportCache
    {
        if (empty($entity))
            $entity = $this->getEntity();
        $this->setEntity($entity);
        $report = $this->getEntityManager()->getRepository(ReportCache::class)->findOneBy(['classId' => $entity->getId(), 'className' => get_class($entity)]);
        if ($report)
            $this->setReportId($report->getId());
        return $report;
    }

    /**
     * @return int|null
     */
    public function getReportId(): ?int
    {
        return $this->reportId;
    }

    /**
     * @param int|null $reportId
     * @return ReportManager
     */
    public function setReportId(?int $reportId): ReportManager
    {
        $this->reportId = $reportId;
        return $this;
    }
}