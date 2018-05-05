<?php
namespace App\People\Util;

use App\Address\Util\AddressManager;
use App\Calendar\Util\CalendarManager;
use App\Core\Manager\SettingManager;
use App\Core\Util\UserManager;
use App\Entity\CalendarGradeStudent;
use App\Entity\Student;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class StudentManager extends PersonManager
{
    /**
     * StudentManager constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param AddressManager $addressManager
     * @param SettingManager $settingManager
     * @param UserManager $userManager
     * @param CalendarManager $calendarManager
     */
    public function __construct(EntityManagerInterface $entityManager, AddressManager $addressManager, SettingManager $settingManager, UserManager $userManager, CalendarManager $calendarManager)
    {
        parent::__construct($entityManager, $addressManager, $settingManager, $userManager, $calendarManager);
    }

    /**
     * @param Collection|null $grades
     * @return QueryBuilder
     */
    public function generateStudentList(?Collection $grades): QueryBuilder
    {
        if ($grades->count() == 0)
        {
            $result = $this->getEntityManager()->getRepository(Student::class)->createQueryBuilder('s')
                ->leftJoin('s.calendarGrades', 'g')
                ->leftJoin('g.calendar', 'c')
                ->where('c.id = :calendar')
                ->setParameter('calendar', $this->getCurrentCalendar())
                ->orderBy('s.surname')
                ->addOrderBy('s.firstName');

            return $result;
        }

        $x = [];
        foreach($grades->getIterator() as $grade)
            $x[] = $grade->getId();

        $result = $this->getEntityManager()->getRepository(Student::class)->createQueryBuilder('s')
            ->leftJoin('s.calendarGrades', 'g')
            ->where('g.id IN (:grades)')
            ->setParameter('grades', $x, Connection::PARAM_STR_ARRAY)
            ->orderBy('s.surname')
            ->addOrderBy('s.firstName');
        return $result;
    }
}