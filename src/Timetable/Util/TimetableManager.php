<?php
namespace App\Timetable\Util;

use App\Core\Manager\MessageManager;
use App\Core\Manager\TableManager;
use App\Core\Manager\TabManager;
use App\Core\Manager\TabManagerInterface;
use App\Entity\Timetable;
use App\Entity\TimetableColumn;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Yaml\Yaml;

class TimetableManager extends TabManager
{
    /**
     * @var RequestStack
     */
    private $stack;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var MessageManager
     */
    private $messageManager;

    /**
     * @var string
     */
    private $status = 'default';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var null|Timetable
     */
    private $timetable;

    /**
     * TimetableManager constructor.
     * @param RequestStack $stack
     * @param RouterInterface $router
     */
    public function __construct(RequestStack $stack, RouterInterface $router, MessageManager $messageManager, EntityManagerInterface $entityManager)
    {
        $this->stack = $stack;
        $this->router = $router;
        $this->messageManager = $messageManager;
        $this->entityManager = $entityManager;
    }

    /**
     * @return array
     */
    public function getTabs(): array
    {
        return Yaml::parse("
timetable:
    label: timetable.details.tab
    include: Timetable/display.html.twig
    translation: Timetable
");
    }

    /**
     * @return string
     */
    public function getCollectionScripts(): string
    {
        return '<!-- Collection Scripts -->';
    }

    /**
     * @return MessageManager
     */
    public function getMessageManager(): MessageManager
    {
        return $this->messageManager;
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
     * @return TimetableManager
     */
    public function setStatus(string $status): TimetableManager
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @param $id
     * @return Timetable
     */
    public function find($id, $notEmpty = false): Timetable
    {
        $entity = $this->getEntityManager()->getRepository(Timetable::class)->find($id);

        if (! $entity instanceof Timetable && $notEmpty)
            throw new \InvalidArgumentException('The system must provide an existing timetable identifier.');
        elseif(! $entity instanceof Timetable)
            $entity = new Timetable();

        $this->timetable = $entity;

        return $entity;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * @return Timetable|null
     */
    public function getTimeTable(): ?Timetable
    {
        return $this->timetable;
    }

    /**
     * @return bool
     */
    public function isValidTimetable(): bool
    {
        if ($this->timetable instanceof Timetable && $this->timetable->getId() > 0)
            return true;
        return false;
    }

    /**
     * @param int $cid
     * @return null
     */
    public function removeColumn(int $cid)
    {
        $column = $this->getEntityManager()->getRepository(TimetableColumn::class)->find($cid);

        if (empty($column) || ! $this->getTimeTable()->getColumns()->contains($column)) {
            $this->getMessageManager()->add('warning', 'timetable.column.remove.missing', [], 'Timetable');
            return ;
        }

        if (!$column->canDelete()) {
            $this->getMessageManager()->add('warning', 'timetable.column.remove.locked', [], 'Timetable');
            return ;
        }

        try {
            $this->timetable->removeColumn($column);

            $this->getEntityManager()->persist($this->timetable);
            $this->getEntityManager()->remove($column);
            $this->getEntityManager()->flush();
        } catch (\Exception $e) {
            $this->getMessageManager()->add('danger', 'timetable.column.remove.error', ['%{message}' => $e->getMessage()], 'Timetable');
            return ;
        }

        $this->getMessageManager()->add('success', 'timetable.column.remove.success', [], "Timetable");

        return ;
    }
}