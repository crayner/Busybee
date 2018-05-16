<?php
namespace App\School\Util;

use App\Entity\Activity;
use App\Entity\FaceToFace;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class ClassManager
 * @package App\Timetable\Util
 */
class ClassManager
{
    /**
     * @var integer
     */
    private $activityCount;

    /**
     * @param Activity $activity
     * @return string
     */
    public function getAlert(Activity $activity): string
    {
        if (!$activity instanceof FaceToFace)
            return '';
        if ($activity->getCount() === 0 || $activity->getAlert() === '')
            $this->getActivityCount($activity);

        return $activity->getAlert();
    }

    /**
     * @param FaceToFace $activity
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getActivityCount(FaceToFace $activity): int
    {
        if (!$activity instanceof Activity)
            return 0;

        if (empty($this->activityCount))
            $this->activityCount = [];

        if (isset($this->activityCount[$activity->getId()]))
            return $this->activityCount[$activity->getId()];

        $result = $this->getEntityManager()->getRepository(Activity::class)->createQueryBuilder('f')
            ->leftJoin('f.periods', 'p')
            ->select('COUNT(p.id)')
            ->where('f.id = :face_id')
            ->setParameter('face_id', $activity->getId())
            ->getQuery()
            ->getSingleScalarResult();

        $activity
            ->setCount(intval($result))
            ->getAlert();

        $this->activityCount[$activity->getId()] = intval($result);
        return $result;
    }

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * ClassManager constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager(): EntityManager
    {
        return $this->entityManager;
    }
}