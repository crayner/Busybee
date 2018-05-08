<?php
namespace App\Timetable\Util;

use App\Calendar\Util\CalendarManager;
use App\Core\Manager\MessageManager;
use App\Core\Manager\SettingManager;
use App\Entity\Calendar;
use App\Entity\Course;
use App\Entity\TimetableLine;
use App\Entity\Student;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Hillrange\Form\Util\CollectionManager;

class LineManager
{
    /**
     * @var null|TimetableLine
     */
    private $line;

    /**
     * @var MessageManager
     */
    private $messageManager;

    /**
     * @param $id
     * @return TimetableLine
     */
    public function find($id): TimetableLine
    {
        $this->line = $this->getEntityManager()->getRepository(TimetableLine::class)->find($id);
        if (! $this->line instanceof TimetableLine)
            $this->line = new TimetableLine();

        $this->line->setCalendar($this->getCalendar());
        return $this->line;
    }

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * LineManager constructor.
     * @param EntityManagerInterface $entityManager
     * @param CalendarManager $calendarManager
     */
    public function __construct(EntityManagerInterface $entityManager, CalendarManager $calendarManager)
    {
        $this->entityManager = $entityManager;
        $this->setCalendar($calendarManager->getCurrentCalendar());
        $this->messageManager = $calendarManager->getMessageManager();
        $this->messageManager->setDomain('Timetable');
    }

    /**
     * Current Calednar
     * @var Calendar
     */
    private $calendar;

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
            $this->getMessageManager()->add('success', 'line.course.remove.success', ['%{name}' => $course->getName()]);
            return ;
        }

        $this->getMessageManager()->add('warning', 'line.course.remove.missing', []);
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
}