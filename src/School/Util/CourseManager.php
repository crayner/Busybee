<?php
namespace App\School\Util;

use App\Core\Manager\MessageManager;
use App\Core\Manager\TabManagerInterface;
use App\Entity\Course;
use App\Entity\FaceToFace;
use App\Entity\Student;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Hillrange\Form\Util\CollectionManager;

class CourseManager implements TabManagerInterface
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

    /**
     * @return array
     */
    public function getTabs(): array
    {
        return [
            'details' => [
                'label' => 'course.details.tab',
                'include' => 'School\course_details.html.twig',
                'message' => 'courseDetailsMessage',
                'translation' => 'School',
            ],
            'classList' => [
                'label' => 'course.class_list.tab',
                'include' => 'School\course_class_list.html.twig',
                'message' => 'courseClassListMessage',
                'translation' => 'School',
            ],
        ];
    }

    /**
     * @return string
     */
    public function getResetScripts(): string
    {
        return '';
    }

    /**
     * @param string $method
     * @return bool
     */
    public function isDisplay(string $method = ''): bool
    {
        return true;
    }

    /**
     * @var integer
     */
    private $currentStudentCount;

    /**
     * @param Course $course
     * @return int
     */
    public function getCurrentStudents(Course $course): int
    {
        $students = 0;
        foreach($course->getActivities()->getIterator() as $class)
        {
            $students += $class->getStudents()->count();
        }
        $this->currentStudentCount = $students;

        return $students;
    }

    /**
     * @param Course|null $course
     * @return int
     */
    public function getCurrentStudentCount(Course $course = null): int
    {
        if ($course instanceof Course)
            return $this->getCurrentStudents($course);
        return 0;
    }

    /**
     * @var Course
     */
    private $course;

    /**
     * @return Course
     */
    public function getCourse(): Course
    {
        return $this->course;
    }

    /**
     * @param Course $course
     * @return CourseManager
     */
    public function setCourse(Course $course): CourseManager
    {
        $this->course = $course;
        return $this;
    }

    /**
     * find
     *
     * @param $id
     * @param bool $create
     * @return Course|null
     */
    public function find($id, bool $create = false): ?Course
    {
        $this->course = $this->getEntityManager()->getRepository(Course::class)->find($id);
        if (empty($this->course) && $create)
            $this->course = new Course();
        return $this->course;
    }

    /**
     * removeActivity
     *
     * @param $id
     * @return bool
     */
    public function removeActivity($id): bool
    {
        $this->findActivity($id);
        if (empty($this->getActivity()))
        {
            $this->getMessageManager()->add('warning', 'course.activity.remove.missing');
            return false;
        }

        if (! $this->getActivity()->canRemoveActivityFromCourse())
        {
            $this->getMessageManager()->add('warning', 'course.activity.remove.locked', ['%{activity}' => $this->getActivity()->getFullName()]);
            return false;
        }

        try {
            $this->getCourse()->removeActivity($this->getActivity());
            $this->getEntityManager()->persist($this->getCourse());
            $this->getEntityManager()->flush();
        } catch (\Exception $e)
        {
            $this->getMessageManager()->add('danger', 'course.activity.remove.error', ['%{activity}' => $this->getActivity()->getFullName(), '%{message}' => $e->getMessage()]);
            return false;
        }
        $this->getMessageManager()->add('success', 'course.activity.remove.success', ['%{activity}' => $this->getActivity()->getFullName()]);
        return true;
    }

    /**
     * @var FaceToFace
     */
    private $activity;

    /**
     * @return FaceToFace
     */
    public function getActivity(): FaceToFace
    {
        return $this->activity;
    }

    /**
     * @param FaceToFace $activity
     * @return CourseManager
     */
    public function setActivity(FaceToFace $activity): CourseManager
    {
        $this->activity = $activity;
        return $this;
    }

    /**
     * findActivity
     *
     * @param $id
     * @param bool $create
     * @return FaceToFace|null
     */
    public function findActivity($id, bool $create = false): ?FaceToFace
    {
        $this->activity = $this->getEntityManager()->getRepository(FaceToFace::class)->find($id);
        if (empty($this->activity) && $create)
            $this->activity = new FaceToFace();
        return $this->activity;
    }
}