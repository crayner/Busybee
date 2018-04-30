<?php
namespace App\School\Util;

use App\Core\Exception\Exception;
use App\Core\Manager\MessageManager;
use App\Core\Manager\TabManagerInterface;
use App\Entity\Activity;
use App\Entity\ActivitySlot;
use App\Entity\ActivityStudent;
use App\Entity\ActivityTutor;
use App\Entity\ExternalActivity;
use App\Entity\FaceToFace;
use App\Entity\Roll;
use App\Entity\Student;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Handler\GelfHandlerLegacyTest;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Yaml\Yaml;

class ActivityManager implements TabManagerInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var string
     */
    private $activityType;

    /**
     * @var null|Activity
     */
    private $activity;

    /**
     * @var MessageManager
     */
    private $messageManager;

    /**
     * @var string
     */
    private $status;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var RequestStack
     */
    private $stack;

    /**
     * ActivityManager constructor.
     * @param TranslatorInterface $translator
     * @param EntityManagerInterface $entityManager
     * @param MessageManager $messageManager
     */
    public function __construct(TranslatorInterface $translator, EntityManagerInterface $entityManager, MessageManager $messageManager, RequestStack $stack, RouterInterface $router)
    {
        $this->translator = $translator;
        $this->entityManager = $entityManager;
        $this->messageManager = $messageManager;
        $this->messageManager->setDomain('School');
        $this->router = $router;
        $this->stack = $stack;
    }

    /**
     * @param ExternalActivity $activity
     * @return string
     */
    public function getTermsGrades(ExternalActivity $activity): string
    {
        $result = '';
        if ($activity->getTerms()->count() == 0)
            $result .= $this->translator->trans('All Terms', [], 'School');

        foreach($activity->getTerms()->getIterator() as $term)
            $result .= $term->getNameShort() . ', ';

        $result = trim($result, ', '). "<br />\n";

        if ($activity->getCalendarGrades()->count() > 0)
        {
            foreach($activity->getCalendarGrades()->getIterator() as $grade)
                $result .= $grade->getGrade(). ', ';
        } elseif ($activity->getCalendarGrades()->count() == 0)
            $result .= $this->translator->trans('All Grades', [], 'School');

        $result = trim($result, ', ');

        return $result;
    }

    /**
     * @return array
     */
    public function getTabs(): array
    {
        $this->isActivityType();
        switch ($this->getActivityType())
        {
            case 'external':
                return Yaml::parse("
external_activity_details:
    label: activity.external.details.tab
    include: School/external_activity_details.html.twig
    message: activityDetailsMessage
    translation: School
external_activity_students:
    label: activity.external.students.tab
    include: School/external_activity_students.html.twig
    message: activityStudentMessage
    translation: School
external_activity_tutors:
    label: activity.external.tutors.tab
    include: School/external_activity_tutors.html.twig
    message: activityTutorMessage
    translation: School
external_activity_slots:
    label: activity.external.slots.tab
    include: School/external_activity_slots.html.twig
    message: activitySlotMessage
    translation: School
");
                break;
            case 'class':
                return Yaml::parse("
class_details:
    label: class.details.tab
    include: School/class_details.html.twig
    message: classDetailsMessage
    translation: School
class_students:
    label: class.students.tab
    include: School/class_students.html.twig
    message: classStudentsMessage
    translation: School
");
                break;
            default:
                throw new Exception('Activity type is not defined. ' . $this->getActivityType() );
        }
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * @return null|string
     */
    public function getActivityType(): ?string
    {
        return strtolower($this->activityType);
    }

    /**
     * @param string $activityType
     * @return ActivityManager
     */
    public function setActivityType(string $activityType): ActivityManager
    {
        $this->activityType = strtolower($activityType);
        return $this;
    }

    /**
     * @param $id
     * @return Activity
     */
    public function findActivity($id): Activity
    {
        $this->isActivityType();

        switch($this->getActivityType())
        {
            case 'roll':
                $activity = $this->entityManager->getRepository(Roll::class)->find($id) ?: new Roll();
                break;
            case 'external':
                $activity = $this->entityManager->getRepository(ExternalActivity::class)->find($id) ?: new ExternalActivity();
                break;
            case 'class':
                $activity = $this->entityManager->getRepository(FaceToFace::class)->find($id) ?: new FaceToFace();
                break;
            default:
                throw new Exception('Activity type is not defined. ' . $this->getActivityType() );
        }

        $this->setActivity($activity);

        return $activity;
    }

    /**
     * @return Activity|null
     */
    public function getActivity(): ?Activity
    {
        return $this->activity;
    }

    /**
     * @param Activity|null $activity
     * @return ActivityManager
     */
    public function setActivity(?Activity $activity): ActivityManager
    {
        $this->activity = $activity;
        return $this;
    }

    /**
     * @throws Exception
     */
    private function isActivityType()
    {
        if (empty($this->getActivityType()))
            throw new Exception('Failed to see a valid activity type.');
    }

    /**
     * @param $id
     */
    public function removeTutor($id)
    {
        $tutor = $this->getEntityManager()->getRepository(ActivityTutor::class)->find($id);
        if (! $tutor instanceof ActivityTutor) {
            $this->messageManager->add('danger', 'activity.tutor.missing.message');
            $this->setStatus('danger');
            return;
        }
        if (! $tutor->canDelete()) {
            $this->messageManager->add('warning', 'activity.tutor.remove.restricted', ['%{tutor}' => $tutor->getTutor()->getFullName()]);
            $this->setStatus('warning');
            return;
        }
        $this->getActivity()->removeTutor($tutor);
        $this->getEntityManager()->remove($tutor);
        $this->getEntityManager()->persist($this->getActivity());
        $this->getEntityManager()->flush();

        $this->messageManager->add('success', 'activity.tutor.removed.message', ['%{tutor}' => $tutor->getTutor()->getFullName()]);
        $this->setStatus('success');
        return;
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
     * @return ActivityManager
     */
    public function setStatus(string $status): ActivityManager
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @param $id
     */
    public function removeStudent($id)
    {
        if (empty($id) || $id == 'ignore')
        {
            $this->setStatus('info');
            return;
        }

        $student = $this->getEntityManager()->getRepository(ActivityStudent::class)->find($id);
        if (! $student instanceof ActivityStudent) {
            $this->messageManager->add('danger', 'activity.student.missing.message');
            $this->setStatus('danger');
            return;
        }
        if (! $student->canDelete()) {
            $this->messageManager->add('warning', 'activity.student.remove.restricted', ['%{student}' => $student->getStudent()->getFullName()]);
            $this->setStatus('warning');
            return;
        }

        $this->getActivity()->removeStudent($student);
        $this->getEntityManager()->remove($student);
        $this->getEntityManager()->persist($this->getActivity());
        $this->getEntityManager()->flush();

        $this->messageManager->add('success', 'activity.student.removed.message', ['%{student}' => $student->getStudent()->getFullName()]);
        $this->setStatus('success');
        return;
    }

    /**
     * @param $id
     */
    public function removeActivitySlot($id)
    {
        $slot = $this->getEntityManager()->getRepository(ActivitySlot::class)->find($id);
        if (! $slot instanceof ActivitySlot) {
            $this->messageManager->add('danger', 'activity.slot.missing.message');
            $this->setStatus('danger');
            return;
        }
        if (! $slot->canDelete()) {
            $this->messageManager->add('warning', 'activity.slot.remove.restricted', ['%{slot}' => $slot->getDayTime()]);
            $this->setStatus('warning');
            return;
        }

        $this->getActivity()->removeActivitySlot($slot);
        $this->getEntityManager()->remove($slot);
        $this->getEntityManager()->persist($this->getActivity());
        $this->getEntityManager()->flush();

        $this->messageManager->add('success', 'activity.slot.removed.message', ['%{slot}' => $slot->getDayTime()]);
        $this->setStatus('success');
        return;
    }

    /**
     * @var int
     */
    private $possibleStudentCount = 0;

    /**
     * @param FaceToFace|null $activity
     * @return Collection
     */
    public function generatePossibleStudents(FaceToFace $activity = null): Collection
    {
        $activity = $activity ?: $this->getActivity();

        $students = new ArrayCollection();

        $ca = $activity->getCourse()->getActivities();
        $activities = [];
        foreach($ca->getIterator() as $face)
            $activities[] = $face->getId();

        $result1 = $this->getPossibleStudents($activity);

        $result2 = new ArrayCollection($this->getEntityManager()->getRepository(Student::class)->createQueryBuilder('s')
            ->leftJoin('s.activities', 'sa')
            ->leftJoin('sa.activity', 'a')
            ->where('a.id IN (:activities)')
            ->setParameter('activities', $activities, Connection::PARAM_STR_ARRAY)
            ->getQuery()
            ->getResult());

        foreach($result1 as $student)
            if (! $result2->contains($student))
                $students->add($student);


        foreach($activity->getStudents()->getIterator() as $student)
            if (! $students->contains($student->getStudent()))
                $students->add($student->getStudent());

        $iterator = $students->getIterator();
        $iterator->uasort(
            function ($a, $b) {
                return ($a->formatName(['surnameFirst' => true]) < $b->formatName(['surnameFirst' => true])) ? -1 : 1;
            }
        );

        return new ArrayCollection(iterator_to_array($iterator, false));
    }

    /**
     * @param Activity $activity
     * @return array
     */
    public function getPossibleStudents(Activity $activity): array
    {
        $grades = [];
        foreach($activity->getCourse()->getCalendarGrades()->getIterator() as $grade)
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
     * @param Activity|null $activity
     * @return int
     */
    public function getPossibleStudentCount(Activity $activity = null): int
    {
        if ($activity instanceof Activity)
            $this->getPossibleStudents($activity);
        return $this->possibleStudentCount;
    }

    /**
     * @return string
     */
    public function getResetScripts(): string
    {
        $request = $this->stack->getCurrentRequest();

        switch ($this->getActivityType())
        {
            case 'external':
                $xx = "manageCollection('" . $this->router->generate("external_activity_tutor_manage", ["id" => $request->get("id"), "cid" => "ignore"]) . "','tutorCollection', '')\n";
                $xx .= "manageCollection('" . $this->router->generate("external_activity_student_manage", ["id" => $request->get("id"), "cid" => "ignore"]) . "','studentCollection', '')\n";
                $xx .= "manageCollection('" . $this->router->generate("external_activity_slot_manage", ["id" => $request->get("id"), "cid" => "ignore"]) . "','slotCollection', '')\n";

                return $xx;
                break;
            case 'class':
                $xx = "manageCollection('" . $this->router->generate("class_tutor_manage", ["id" => $request->get("id"), "cid" => "ignore"]) . "','tutorCollection', '')\n";
                $xx .= "manageCollection('" . $this->router->generate("class_student_manage", ["id" => $request->get("id"), "cid" => "ignore"]) . "','studentCollection', '')\n";

                return $xx;
                break;
            default:
                throw new Exception('Activity type is not defined. ' . $this->getActivityType() );
        }
    }

    /**
     * @param string $method
     * @return bool
     */
    public function isDisplay(string $method = ''): bool
    {
        return true;
    }
}