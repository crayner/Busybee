<?php
namespace App\School\Util;

use App\Core\Exception\Exception;
use App\Core\Manager\MessageManager;
use App\Entity\Activity;
use App\Entity\ActivitySlot;
use App\Entity\ActivityStudent;
use App\Entity\ActivityTutor;
use App\Entity\ExternalActivity;
use App\Entity\Roll;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Yaml\Yaml;

class ActivityManager
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
     * ActivityManager constructor.
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator, EntityManagerInterface $entityManager, MessageManager $messageManager)
    {
        $this->translator = $translator;
        $this->entityManager = $entityManager;
        $this->messageManager = $messageManager;
        $this->messageManager->setDomain('School');
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
        return $this->activityType;
    }

    /**
     * @param string $activityType
     * @return ActivityManager
     */
    public function setActivityType(string $activityType): ActivityManager
    {
        $this->activityType = $activityType;
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
     * @throws \App\People\Entity\CommonException
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
        $student = $this->getEntityManager()->getRepository(ActivityStudent::class)->find($id);
        if (! $student instanceof ActivityStudent) {
            $this->messageManager->add('danger', 'activity.student.missing.message');
            $this->setStatus('danger');
            return;
        }
        if (! $student->canDelete()) {
            $this->messageManager->add('warning', 'activity.student.remove.restricted', ['%{tutor}' => $student->getTutor()->getFullName()]);
            $this->setStatus('warning');
            return;
        }

        $this->getActivity()->removeStudent($student);
        $this->getEntityManager()->remove($student);
        $this->getEntityManager()->persist($this->getActivity());
        $this->getEntityManager()->flush();

        $this->messageManager->add('success', 'activity.student.removed.message', ['%{tutor}' => $student->getTutor()->getFullName()]);
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
            $this->messageManager->add('warning', 'activity.slot.remove.restricted', ['%{tutor}' => $slot->getTutor()->getFullName()]);
            $this->setStatus('warning');
            return;
        }

        $this->getActivity()->removeStudent($slot);
        $this->getEntityManager()->remove($slot);
        $this->getEntityManager()->persist($this->getActivity());
        $this->getEntityManager()->flush();

        $this->messageManager->add('success', 'activity.slot.removed.message', ['%{tutor}' => $slot->getTutor()->getFullName()]);
        $this->setStatus('success');
        return;
    }
}