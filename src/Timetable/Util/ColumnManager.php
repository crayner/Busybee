<?php
namespace App\Timetable\Util;


use App\Core\Manager\MessageManager;
use App\Core\Manager\SettingManager;
use App\Core\Manager\TabManager;
use App\Core\Manager\TwigManagerInterface;
use App\Entity\TimetableColumn;
use App\Entity\TimetableColumnPeriod;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

class ColumnManager extends TabManager implements TwigManagerInterface
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
     * @var string 
     */
    private $status = 'default';

    /**
     * @var MessageManager 
     */
    private $messageManager;

    /**
     * @var RequestStack
     */
    private $stack;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * ColumnManager constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, SettingManager $settingManager, MessageManager $messageManager, RequestStack $stack, RouterInterface $router)
    {
        $this->entityManager = $entityManager;
        $this->settingManager = $settingManager;
        $this->messageManager = $messageManager;
        $this->stack = $stack;
        $this->router = $router;
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

    /**
     * @param $id
     */
    public function removePeriod($id)
    {
        if (empty($id) || $id == 'ignore')
        {
            $this->setStatus('info');
            return;
        }

        $period = $this->getEntityManager()->getRepository(TimetableColumnPeriod::class)->find($id);

        if (! $period instanceof TimetableColumnPeriod) {
            $this->messageManager->add('danger', 'timetable.column.period.missing.message');
            $this->setStatus('danger');
            return;
        }

        if (! $period->canDelete()) {
            $this->messageManager->add('warning', 'timetable.column.period.remove.restricted', ['%{column}' => $period->getName()]);
            $this->setStatus('warning');
            return;
        }

        $this->getEntity()->removePeriod($period);
        $this->getEntityManager()->remove($period);
        $this->getEntityManager()->persist($this->getEntity());
        $this->getEntityManager()->flush();

        $this->messageManager->add('success', 'timetable.column.period.removed.message', ['%{column}' => $period->getName()]);
        $this->setStatus('success');
        return;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return ColumnManager
     */
    public function setStatus(string $status): ColumnManager
    {
        $this->status = $status;
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
     * @return array
     */
    public function getTabs(): array
    {
        return [];
    }

    /**
     * @return string
     */
    public function getResetScripts(): string
    {
        $request = $this->stack->getCurrentRequest();
        $xx = "manageCollection('" . $this->router->generate("timetable_column_period_remove", ["id" => $request->get("id"), "cid" => "ignore"]) . "','periodCollection', '')\n";

        return $xx;
    }
}