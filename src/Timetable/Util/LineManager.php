<?php
namespace App\Timetable\Util;

use App\Calendar\Util\CalendarManager;
use App\Core\Exception\Exception;
use App\Core\Manager\MessageManager;
use App\Entity\Calendar;
use App\Entity\Course;
use App\Entity\Line;
use Doctrine\ORM\EntityManagerInterface;

class LineManager
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * Current Calednar
     * @var Calendar
     */
    private $calendar;

    /**
     * @var MessageManager
     */
    private $messageManager;

    /**
     * LineManager constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, CalendarManager $calendarManager)
    {
        $this->entityManager = $entityManager;
        $this->setCalendar($calendarManager->getCurrentCalendar());
        $this->messageManager = $calendarManager->getMessageManager();
        $this->messageManager->setDomain('Timetable');
    }

    /**
     * @var null|Line
     */
    private $line;

    /**
     * @param $id
     * @return Line
     */
    public function find($id): Line
    {
        $this->line = $this->getEntityManager()->getRepository(Line::class)->find($id);
        if (! $this->line instanceof Line)
            $this->line = new Line();

        $this->line->setCalendar($this->calendar);
        return $this->line;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * @return Calendar
     */
    public function getCalendar(): Calendar
    {
        return $this->calendar;
    }

    /**
     * @param Calendar $calendar
     * @return LineManager
     */
    public function setCalendar(Calendar $calendar): LineManager
    {
        $this->calendar = $calendar;
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
     * @return LineManager
     */
    public function setStatus(string $status): LineManager
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @param $id
     */
    public function removeCourse($id)
    {
        if (intval($id) == 0)
            return ;

        $course = $this->getEntityManager()->getRepository(Course::class)->find($id);

        if ($course instanceof Course)
        {
            $this->line->removeCourse($course);

            $this->getEntityManager()->persist($course);
            $this->getEntityManager()->flush();
            $this->getMessageManager()->add('success', 'line.course.remove.success', ['%{name}' => $course->getName()], 'Timetable');
            return ;
        }

        $this->getMessageManager()->add('warning', 'line.course.remove.missing', [], 'Timetable');
    }
}