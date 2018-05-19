<?php
namespace App\Timetable\Validator\Constraints;

use App\Core\Manager\SettingManager;
use App\Entity\TimetablePeriod;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;


class PeriodsValidator extends ConstraintValidator
{
    /**
     * @var SettingManager
     */
    private $settingManager;

    /**
     * PeriodsValidator constructor.
     * @param SettingManager $settingManager
     */
    public function __construct(SettingManager $settingManager)
    {
        $this->settingManager = $settingManager;
    }

    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (empty($value))
            return;

        $overlap = false;
        $order = false;
        $break = false;
        $error = false;

        $timeFormat = $this->settingManager->get('time.format.short');

        $iterator = $value->getIterator();
        $iterator->uasort(function ($a, $b) {
            return ($a->getStart() < $b->getStart()) ? -1 : 1;
        });

        $column = $value->first()->getColumn();

        $value = new ArrayCollection(iterator_to_array($iterator, false));

        $data = $value->toArray();
        $x = reset($data);

        if ($x instanceof TimetablePeriod) {
            $start = $x->getStart();
            $end = $x->getEnd();
            $keep = clone $x;
        } else {
            $start = $column->getStart();
            $end = $column->getEnd();
            $keep = new TimetablePeriod();
        }
        $q = 0;

        $previous = reset($data);
        while (! $error && false !== ($period = next($data))) {
            if (!$error) {
                if ($period->getStart() < $end) {
                    $error = true;
                    $overlap = true;
                    $keep = clone $period;
                }
                if ($period->getStart() >= $period->getEnd()) {
                    $error = true;
                    $order = true;
                    $keep = clone $period;
                }
                if ($period->getStart() > $end) {
                    $error = true;
                    $break = true;
                    $keep = clone $period;
                }

                $end = $period->getEnd();
                $q++;

            }
            if (!$error)
                $previous = current($data);
        }


        if ($order) {
            $this->context->buildViolation($constraint->message['order'])
                ->atPath('[' . $q . '].start')
                ->setTranslationDomain('Timetable')
                ->addViolation();
        }
        if ($overlap) {
            $this->context->buildViolation($constraint->message['overlap'])
                ->atPath('[' . $q . '].start')
                ->setParameter('%end%', $previous->getEnd()->format($timeFormat))
                ->setTranslationDomain('Timetable')
                ->addViolation();
        }
        if ($break) {
            $this->context->buildViolation($constraint->message['break'])
                ->atPath('[' . $q . '].start')
                ->setParameter('%end%', $previous->getEnd()->format($timeFormat))
                ->setTranslationDomain('Timetable')
                ->addViolation();
        }
        if ($start < $column->getStart()) {
            $this->context->buildViolation($constraint->message['early'])
                ->atPath('[0].start')
                ->setParameter('%limit%', $column->getStart()->format($timeFormat))
                ->setTranslationDomain('Timetable')
                ->addViolation();
        }
        if ($end > $column->getEnd()) {
            $this->context->buildViolation($constraint->message['late'])
                ->atPath('[' . ($value->count() - 1) . '].end')
                ->setParameter('%limit%', $column->getEnd()->format($timeFormat))
                ->setTranslationDomain('Timetable')
                ->addViolation();
        }

        if ($end < $column->getEnd()) {
            $this->context->buildViolation($constraint->message['complete'])
                ->atPath('[' . ($value->count() - 1) . '].end')
                ->setParameter('%end%', $column->getEnd()->format($timeFormat))
                ->setParameter('%start%', $column->getStart()->format($timeFormat))
                ->setTranslationDomain('Timetable')
                ->addViolation();
        }
        if ($start > $column->getStart()) {
            $this->context->buildViolation($constraint->message['complete'])
                ->atPath('[0].start')
                ->setParameter('%end%', $column->getEnd()->format($timeFormat))
                ->setParameter('%start%', $column->getStart()->format($timeFormat))
                ->setTranslationDomain('Timetable')
                ->addViolation();
        }

        return $value;
    }
}