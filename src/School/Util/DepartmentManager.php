<?php
namespace App\School\Util;

use App\Core\Manager\MessageManager;
use App\Entity\Course;
use App\Entity\Department;
use App\Entity\DepartmentMember;
use App\Entity\Staff;
use Doctrine\ORM\EntityManagerInterface;
use Hillrange\Form\Util\CollectionInterface;

class DepartmentManager implements CollectionInterface
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
     * @var null|Staff
     */
    private $member;

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
            $this->entityManager->persist($this->course);
            $this->entityManager->persist($this->department);
            $this->entityManager->flush();
            $this->entityManager->refresh($this->department);
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

    /**
     * @param $cid
     */
    public function removeMember($cid)
    {
        if ($cid === 'ignore')
            return ;

        $this->getDepartment();

        $this->findMember($cid);
        $this->setStatus('warning');

        if (empty($this->member)) {
            $this->messageManager->add('warning', 'department.member.missing.warning', ['%{member}' => $cid]);
            return;
        }

        if ($this->department->getMembers()->contains($this->member)) {
            // Staff is NOT Deleted, but the DepartmentMember link is deleted.
            $this->department->removeMember($this->member);
            $this->entityManager->remove($this->member);
            $this->entityManager->persist($this->department);
            $this->entityManager->flush();
            $this->setStatus('success');

            $this->messageManager->add('success', 'department.member.removed.success', ['%{member}' => $this->member->getFullStaffName()]);
        } else {
            $this->setStatus('info');
            $this->messageManager->add('info', 'department.member.removed.info', ['%{member}' => $this->member->getFullStaffName()]);
        }
    }

    /**
     * @param $id
     * @return Department
     */
    public function findMember($id): ?DepartmentMember
    {
        $this->member = $this->entityManager->getRepository(DepartmentMember::class)->find(intval($id));

        return $this->getMember();
    }

    /**
     * @return Staff|null
     */
    public function getMember(): ?DepartmentMember
    {
        return $this->member;
    }

    /**
     * @return Department|null
     */
    public function refreshDepartment(): ?Department
    {
        if (empty($this->department))
            return $this->department;

        try {
            $this->entityManager->refresh($this->department);
            return $this->department->refresh();
        } catch (\Exception $e) {
            return $this->department;
        }
    }
}