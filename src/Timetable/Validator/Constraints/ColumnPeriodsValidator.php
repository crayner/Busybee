<?php
namespace App\Timetable\Validator\Constraints;

use App\Entity\TimetableColumn;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ColumnPeriodsValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (empty($value))
            return ;

        if ($value instanceof TimetableColumn)
        {
            $iterator = $value->getPeriods()->getIterator();
            $iterator->uasort(function ($a, $b) {
                return ($a->getTimeStart()->format('H:i') < $b->getTimeStart()->format('H:i')) ? -1 : 1;
            });

            $periods = new ArrayCollection(iterator_to_array($iterator, false));

            $last = [];
            foreach ($periods->getIterator() as $q => $period)
            {
                if ($period->getTimeStart()->format('H:i') >= $period->getTimeEnd()->format('H:i'))
                    $this->context->buildViolation('timetable.column.period.time.negative')
                        ->setTranslationDomain('Timetable')
                        ->setParameter('%{name}', $period->getName())
                        ->addViolation();


                // test gap
                if (empty($last) || $last[1]->format('H:i') === $period->getTimeStart()->format('H:i'))
                {
                    $last[0] = $period->getTimeStart();
                    $last[1] = $period->getTimeEnd();
                    continue;
                }
                $this->context->buildViolation('timetable.column.period.time.gap')
                    ->setTranslationDomain('Timetable')
                    ->setParameter('%{time}', $last[1]->format('H:i'))
                    ->setParameter('%{name}', $period->getName())
                    ->addViolation();
                $last[0] = $period->getTimeStart();
                $last[1] = $period->getTimeEnd();
            }
        }
    }
}