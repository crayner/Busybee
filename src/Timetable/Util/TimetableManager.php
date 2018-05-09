<?php
namespace App\Timetable\Util;

use App\Core\Manager\MessageManager;
use App\Core\Manager\TabManager;
use App\Entity\Calendar;
use App\Entity\Timetable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Yaml\Yaml;

class TimetableManager extends TabManager
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * TimetableManager constructor.
     * @param RequestStack $stack
     * @param RouterInterface $router
     */
    public function __construct(EntityManagerInterface $entityManager, MessageManager $messageManager)
    {
        $this->entityManager = $entityManager;
        $this->messageManager = $messageManager;
    }

    /**
     * @var Timetable|null
     */
    private $timetable;

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
     * @return array
     */
    public function getTabs(): array
    {
        $x = Yaml::parse("
timetable:
    label: timetable.details.tab
    include: Timetable/display.html.twig
    translation: Timetable
");
        if ($this->isValidTimetable())
        {
            foreach($this->getCalendar()->getTerms() as $term)
            {
                $w = [];
                $w['label'] = $term->getName();
                $w['translation'] = false;
                $w['include'] = 'Timetable/Day/assign_days.html.twig';
                $w['with'] = ['term' => $term];
                $w['display'] = ['method' =>'isValidTerm', 'with' => ['term' => $term, 'timetable' => $this->getTimetable()]];
                $x[$term->getName()] = $w;
            }
        }

        return $x;
    }

    /**
     * @return bool
     */
    public function isValidTimetable(): bool
    {
        if ($this->getTimetable() instanceof Timetable && $this->getTimetable()->getId() > 0)
            return true;
        return false;
    }

    /**
     * @return Timetable|null
     */
    public function getTimetable(): ?Timetable
    {
        return $this->timetable;
    }

    /**
     * @return Calendar
     */
    public function getCalendar(): Calendar
    {
        return $this->getTimetable()->getCalendar();
    }

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
     * @return TimetableManager
     */
    public function setStatus(string $status): TimetableManager
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @var MessageManager
     */
    private $messageManager;

    /**
     * @return MessageManager
     */
    public function getMessageManager(): MessageManager
    {
        return $this->messageManager;
    }
}