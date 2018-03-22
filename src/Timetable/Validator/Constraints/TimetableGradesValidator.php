<?php
namespace App\Timetable\Validator\Constraints;

use App\Entity\CalendarGrade;
use App\Entity\Timetable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class TimetableGradesValidator extends ConstraintValidator
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var RequestStack
     */
    private $stack;

    /**
     * TimetableGradesValidator constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, RequestStack $stack)
    {
        $this->entityManager = $entityManager;
        $this->stack = $stack;
    }

    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (count($value) === 0)
            return;

        $cal = reset($value);
        $cal_id = intval($cal->getCalendar()->getId());

        $tt_id = intval($this->stack->getCurrentRequest()->get('id'));

        $result = $this->entityManager->getRepository(Timetable::class)
            ->createQueryBuilder('t')
            ->leftJoin('t.calendar', 'c')
            ->where('c.id = :cal_id')
            ->andWhere('t.id != :tt_id')
            ->setParameter('cal_id', $cal_id)
            ->setParameter('tt_id', $tt_id)
            ->getQuery()
            ->getResult();

        if (is_array($result))
        {
             $q = new ArrayCollection();
             foreach($result as $tt)
                 foreach($tt->getCalendarGrades()->getIterator() as $w)
                    $q->add($w);

             $result = $q;
        }
        else
            $result = new ArrayCollection();

        foreach($value as $cg){
            if ($result->contains($cg))
                $this->context->buildViolation($constraint->message)
                    ->setTranslationDomain('Timetable')
                    ->setParameter('%{grade}', $cg->getGrade())
                    ->addViolation();

        }
    }
}