<?php
namespace App\School\Util;

use App\Core\Manager\MessageManager;
use App\Entity\Course;
use App\Entity\Department;
use Doctrine\ORM\EntityManagerInterface;

class DepartmentManager
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var null|Department
     */
    private $department;

    /**
     * @var null|Course
     */
    private $course;

    /**
     * @var MessageManager
     */
    private $messageManager;

    /**
     * @var string
     */
    private $status;

    /**
     * DepartmentManager constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, MessageManager $messageManager)
    {
        $this->entityManager = $entityManager;
        $this->messageManager = $messageManager;
        $this->messageManager->setDomain('School');
    }

    /**
     * @param $id
     * @return Department
     */
    public function findDepartment($id): Department
    {
        $this->department = $this->entityManager->getRepository(Department::class)->find(intval($id));
        return $this->getDepartment();
    }

    /**
     * @return Department
     */
    public function getDepartment(): Department
    {
        if (empty($this->department))
            $this->department = new Department();

        return $this->department;
    }

    /**
     * @param $cid
     */
    public function removeCourse($cid)
    {
        $this->getDepartment();

        $this->findCourse($cid);
        $this->setStatus('warning');

        if ($cid === 'ignore')
            return ;

        if (empty($this->course)) {
            $this->messageManager->add('warning', 'department.course.missing.warning', ['%{course}' => $cid]);
            return;
        }

        if ($this->department->getCourses()->contains($this->course)) {
            // Course is NOT Deleted, only removed from Department.
            $this->department->removeCourse($this->course);
            $this->entityManager->persist($this->department);
            $this->entityManager->flush();
            $this->setStatus('success');

            $this->messageManager->add('success', 'department.course.removed.success', ['%{course}' => $this->course->getFullName()]);
        } else {
            $this->setStatus('info');
            $this->messageManager->add('info', 'department.course.removed.info', ['%{course}' => $this->course->getFullName()]);
        }
    }

    /**
     * @param $id
     * @return Department
     */
    public function findCourse($id): ?Course
    {
        $this->course = $this->entityManager->getRepository(Course::class)->find(intval($id));

        return $this->getCourse();
    }

    /**
     * @return null|Course
     */
    public function getCourse(): ?Course
    {
        return $this->course;
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
        return empty($this->status) ? 'default' : $this->status;
    }

    /**
     * @param string $status
     * @return DepartmentManager
     */
    public function setStatus(string $status): DepartmentManager
    {
        $this->status = $status;
        return $this;
    }
}