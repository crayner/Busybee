<?php
namespace App\School\Util;

use App\Core\Exception\Exception;
use App\Entity\Activity;
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
     * ActivityManager constructor.
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator, EntityManagerInterface $entityManager)
    {
        $this->translator = $translator;
        $this->entityManager = $entityManager;
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
            throw new Exception('Failed to see an activity type.');
    }
}