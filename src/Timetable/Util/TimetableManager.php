<?php
namespace App\Timetable\Util;

use App\Core\Manager\MessageManager;
use App\Core\Manager\TabManager;
use App\Entity\Timetable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

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
    private $timeTable;

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
        return [];
    }

    /**
     * @return string
     */
    public function getResetScripts(): string
    {
        $request = $this->stack->getCurrentRequest();
        $xx = "manageCollection('" . $this->router->generate("timetable_days_edit", ["id" => $request->get("id"), "cid" => "ignore"]) . "','courseCollection', '')\n";
//        $xx .= "manageCollection('" . $this->router->generate("department_members_manage", ["id" => $request->get("id"), "cid" => "ignore"]) . "','memberCollection', '')\n";

        return $xx;
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

        $this->timeTable = $entity;

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
        return $this->timeTable;
    }
}