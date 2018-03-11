<?php
namespace App\School\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Hillrange\Security\Util\UserTrackInterface;
use Hillrange\Security\Util\UserTrackTrait;
use Symfony\Component\Yaml\Yaml;

abstract class DepartmentExtension implements UserTrackInterface
{
	use UserTrackTrait;
    /**
     * @var bool
     */
	protected $membersSorted = false;

    /**
     * @var bool
     */
    protected $coursesSorted = false;

    /**
     * @var array
     */
    private $staff;

    /**
     * Sort Members
     *
     * @return ArrayCollection
     */
	protected function sortMembers()
    {
	    if (count($this->getMembers(false)) == 0 || $this->membersSorted)
		    return $this->getMembers(false);

	    $iterator = $this->getMembers(false)->getIterator();
        $iterator->uasort(function ($a, $b) {
            return ($a->getStaff()->getFullName() < $b->getStaff()->getFullName()) ? -1 : 1;
        });

	    $members = new ArrayCollection(iterator_to_array($iterator, false));

	    $this->membersSorted = true;
	    $this->setMembers($members);

	    return $this->getMembers(false);
    }

    /**
     * Sort Courses
     *
     * @return ArrayCollection
     */
    protected function sortCourses()
    {
        if (count($this->getCourses(false)) == 0 || $this->coursesSorted)
            return $this->getCourses(false);

        $iterator = $this->getCourses(false)->getIterator();
        $iterator->uasort(function ($a, $b) {
            return ($a->getName() < $b->getName()) ? -1 : 1;
        });

        $courses = new ArrayCollection(iterator_to_array($iterator, false));

        $this->coursesSorted = true;
        $this->setCourses($courses);

        return $this->getCourses(false);
    }

    protected function memberInitialise($members)
    {
        $members->initialize();
        $staff = new ArrayCollection();

        foreach($members->toArray() as $member)
            $staff[intval($member->getId())] =  $member;
    }

    /**
     * @return ArrayCollection
     */
    public function getStaff(): array
    {
        if (empty($this->staff))
            $this->staff = [];

        return $this->staff;
    }

    /**
     * @return array
     */
    public function getTabs(): array
    {
        return Yaml::parse("
department_details:
    label: department.details.tab
    include: Department/department_details.html.twig
    message: departmentDetailsMessage
    translation: School
department_tutor_collection:
    label: department.staff.tab
    include: Department/department_staff.html.twig
    message: departmentStaffMessage
    translation: School
department_course_collection:
    label: department.courses.tab
    include: Department/department_courses.html.twig
    message: departmentCoursesMessage
    translation: School
");
    }
}