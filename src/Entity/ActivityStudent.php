<?php
namespace App\Entity;

use Hillrange\Security\Util\UserTrackInterface;
use Hillrange\Security\Util\UserTrackTrait;

class ActivityStudent implements UserTrackInterface
{
    use UserTrackTrait;

    /**
     * @var null|int
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
     * @var null|Student
     */
    private $student;

    /**
     * @return Student|null
     */
    public function getStudent(): ?Student
    {
        return $this->student;
    }

    /**
     * @param Student|null $student
     * @return ActivityStudent
     */
    public function setStudent(?Student $student, $add = true): ActivityStudent
    {
        if (empty($student))
            return $this;

        if ($add)
            $student->addActivity($this, false);

        $this->student = $student;

        return $this;
    }

    /**
     * @var null|Activity
     */
    private $activity;

    /**
     * @return Activity|null
     */
    public function getActivity(): ?Activity
    {
        return $this->activity;
    }

    /**
     * @param Activity|null $activity
     * @return ActivityStudent
     */
    public function setActivity(?Activity $activity, $add = true): ActivityStudent
    {
        if (empty($activity))
            return $this;

        if ($add)
            $activity->addStudent($this, false);

        $this->activity = $activity;

        return $this;
    }

    /**
     * @var null|string
     */
    private $externalStatus;

    /**
     * @return null|string
     */
    public function getExternalStatus(): ?string
    {
        return $this->externalStatus;
    }

    /**
     * @param null|string $externalStatus
     * @return ActivityStudent
     */
    public function setExternalStatus(?string $externalStatus): ActivityStudent
    {
        $this->externalStatus = $externalStatus;
        return $this;
    }

    /**
     * @var null|\DateTime
     */
    private $externalTimestamp;

    /**
     * @return \DateTime|null
     */
    public function getExternalTimestamp(): ?\DateTime
    {
        return $this->externalTimestamp;
    }

    /**
     * @param \DateTime|null $externalTimestamp
     * @return ActivityStudent
     */
    public function setExternalTimestamp(?\DateTime $externalTimestamp): ActivityStudent
    {
        $this->externalTimestamp = $externalTimestamp;
        return $this;
    }

    /**
     * @var boolean
     */
    private $externalInvoiceGenerated;

    /**
     * @return bool
     */
    public function isExternalInvoiceGenerated(): bool
    {
        return $this->externalInvoiceGenerated ? true : false;
    }

    /**
     * @param null|bool $externalInvoiceGenerated
     * @return ActivityStudent
     */
    public function setExternalInvoiceGenerated(?bool $externalInvoiceGenerated): ActivityStudent
    {
        $this->externalInvoiceGenerated = $externalInvoiceGenerated ? true : false;
        return $this;
    }

    /**
     * @var null|Invoice
     */
    private $externalInvoiceID;

    /**
     * @return Invoice|null
     */
    public function getExternalInvoiceID(): ?Invoice
    {
        return $this->externalInvoiceID;
    }

    /**
     * @param Invoice|null $externalInvoiceID
     * @return ActivityStudent
     */
    public function setExternalInvoiceID(?Invoice $externalInvoiceID): ActivityStudent
    {
        $this->externalInvoiceID = $externalInvoiceID;
        return $this;
    }

    /**
     * @var null|Activity
     */
    private $externalActivityBackup;

    /**
     * @return Activity|null
     */
    public function getExternalActivityBackup(): ?Activity
    {
        return $this->externalActivityBackup;
    }

    /**
     * @param Activity|null $externalActivityBackup
     * @return ActivityStudent
     */
    public function setExternalActivityBackup(?Activity $externalActivityBackup): ActivityStudent
    {
        $this->externalActivityBackup = $externalActivityBackup;
        return $this;
    }

    /**
     * @var null|bool
     */
    private $classReportable;

    /**
     * @return bool|null
     */
    public function getClassReportable(): bool
    {
        return $this->classReportable ? true : false;
    }

    /**
     * @param bool|null $classReportable
     * @return ActivityStudent
     */
    public function setClassReportable(?bool $classReportable): ActivityStudent
    {
        $this->classReportable = $classReportable ? true : false ;

        return $this;
    }
}