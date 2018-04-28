<?php
namespace App\Timetable\Util;

use App\Core\Manager\SettingManager;
use App\Entity\TimetableColumn;
use App\Entity\TimetablePeriod;
use Doctrine\ORM\EntityManagerInterface;

class ColumnManager
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var SettingManager
     */
    private $settingManager;

    /**
     * ColumnManager constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, SettingManager $settingManager)
    {
        $this->entityManager = $entityManager;
        $this->settingManager = $settingManager;
    }

    /**
     * @var TimetableColumn|null
     */
    private $column;

    /**
     * @param null|string|integer $id
     * @return TimetableColumn|null
     */
    public function find($id): ?TimetableColumn
    {
        $this->column = $this->getEntityManager()->getRepository(TimetableColumn::class)->find(intval($id));
        return $this->column;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * @return TimetableColumn|null
     */
    public function getColumn(): ?TimetableColumn
    {
        return $this->column;
    }

    /**
     * @param TimetableColumn|null $column
     * @return ColumnManager
     */
    public function setColumn(?TimetableColumn $column): ColumnManager
    {
        $this->column = $column;
        return $this;
    }

    public function generatePeriods()
    {
        if ($this->getColumn()->getPeriods()->count() > 0)
            return;

        $periods = $this->getSettingManager()->get('schoolday.periods');

        foreach($periods as $name => $value)
        {
            $period = new TimetablePeriod();
            $period->setName($name);
            $period->setCode(isset($value['code']) ? $value['code'] : mb_substr($name, 0, 3));
            $period->setStart(new \DateTime('1970-01-01 '.$value['start']));
            $period->setEnd(new \DateTime('1970-01-01 '.$value['end']));
            $period->setPeriodType(isset($value['type']) ? $value['type'] : 'class');
            $this->getColumn()->addPeriod($period);
            $this->getEntityManager()->persist($period);
        }
        $this->getEntityManager()->persist($this->getColumn());
        $this->getEntityManager()->flush();

        dump($this);

    }

    /**
     * @return SettingManager
     */
    public function getSettingManager(): SettingManager
    {
        return $this->settingManager;
    }
}