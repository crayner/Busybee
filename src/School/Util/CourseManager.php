<?php
namespace App\School\Util;

use App\Core\Manager\MessageManager;
use App\Entity\Course;
use App\Entity\Student;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;

class CourseManager
{
    /**
     * @var int
     */
    private $possibleStudentCount = 0;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var MessageManager
     */
    private $messageManager;

    /**
     * CourseManager constructor.
     * @param EntityManagerInterface $entityManager
     * @param MessageManager $messageManager
     */
    public function __construct(EntityManagerInterface $entityManager, MessageManager $messageManager)
    {
        $this->entityManager = $entityManager;
        $this->messageManager = $messageManager;
        $this->messageManager->setDomain('School');
    }

    /**
     * @param Course $course
     * @return array
     */
    public function getPossibleStudents(Course $course): array
    {
        $grades = [];
        foreach($course->getCalendarGrades()->getIterator() as $grade)
            $grades[] = $grade->getId();

        $result = $this->getEntityManager()->getRepository(Student::class)->createQueryBuilder('s')
            ->leftJoin('s.calendarGrades', 'cg')
            ->where('cg.id IN (:grades)')
            ->setParameter('grades', $grades, Connection::PARAM_STR_ARRAY)
            ->getQuery()
            ->getResult();

        $this->possibleStudentCount = count($result);

        return $result;
    }

    /**
     * @param Course|null $course
     * @return int
     */
    public function getPossibleStudentCount(Course $course = null): int
    {
        if ($course instanceof Course)
            $this->getPossibleStudents($course);
        return $this->possibleStudentCount;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * @return MessageManager
     */
    public function getMessageManager(): MessageManager
    {
        return $this->messageManager;
    }
}