<?php
namespace App\Timetable\Util;


use App\Core\Manager\SettingManager;
use App\Entity\TimetableColumn;
use App\Entity\TimetableColumnPeriod;
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
    private $entity;

    /**
     * @param int|null $id
     * @return TimetableColumn|null
     */
    public function find(?int $id): ?TimetableColumn
    {
        $this->entity = $this->getEntityManager()->getRepository(TimetableColumn::class)->find($id);

        $this->createPeriods();

        return $this->entity;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * @return TimetableColumn
     */
    public function getEntity(): TimetableColumn
    {
        if (empty($this->entity))
            $this->entity = new TimetableColumn();

        return $this->entity;
    }


    /**
     * Create Periods
     */
    private function createPeriods()
    {
        if (empty($this->entity) || $this->entity->getPeriods()->count() > 0)
            return;

        $periods = $this->settingManager->get('schoolday.periods');

        foreach($periods as $name => $details)
        {
            $period = new TimetableColumnPeriod();
            $start = new \DateTime($details['start']);
            $end = new \DateTime($details['end']);
            dump([$start,$end]);
            $period
                ->setName($name)
                ->setCode($details['code'])
                ->setTimeStart($start)
                ->setTimeEnd($end)
                ->setType(isset($details['type']) ? $details['type'] : 'lesson');
            $this->entity->addPeriod($period);
        }
        $this->getEntityManager()->persist($this->entity);
        $this->getEntityManager()->flush();
    }
}