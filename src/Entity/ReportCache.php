<?php
namespace App\Entity;

use App\Core\Util\ReportInterface;
use Hillrange\Security\Util\UserTrackInterface;
use Hillrange\Security\Util\UserTrackTrait;

class ReportCache implements UserTrackInterface
{
    use UserTrackTrait;
    /**
     * @var integer|null
     */
    private $id;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @var string|null
     */
    private $className;

    /**
     * @return null|string
     */
    public function getClassName(): ?string
    {
        return $this->className;
    }

    /**
     * @param null|string $className
     * @return ReportCache
     */
    public function setClassName(?string $className): ReportCache
    {
        $this->className = $className;
        return $this;
    }

    /**
     * @var integer|null
     */
    private $classId;

    /**
     * @return int|null
     */
    public function getClassId(): ?int
    {
        return $this->classId;
    }

    /**
     * @param int|null $classId
     * @return ReportCache
     */
    public function setClassId(?int $classId): ReportCache
    {
        $this->classId = $classId;
        return $this;
    }

    /**
     * @var ReportInterface|null
     */
    private $report;

    /**
     * @return ReportInterface|null
     */
    public function getReport(): ?ReportInterface
    {
        return $this->report;
    }

    /**
     * @param ReportInterface|null $report
     * @return ReportCache
     */
    public function setReport(?ReportInterface $report): ReportCache
    {
        $this->report = $report;
        return $this;
    }
}