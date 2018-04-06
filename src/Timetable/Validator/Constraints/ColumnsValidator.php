<?php
namespace App\Timetable\Validator\Constraints;

use App\Core\Manager\SettingManager;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ColumnsValidator extends ConstraintValidator
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

        if ($value instanceof PersistentCollection && $value->count() == 0)
            return;

        if ($this->settingManager->get('schoolday.open') instanceof \DateTime)
            $open = $this->settingManager->get('schoolday.open');
        else
            $open = new \DateTime('1970-01-01 ' . $this->settingManager->get('schoolday.open'));

        if ($this->settingManager->get('schoolday.close') instanceof \DateTime)
            $close = $this->settingManager->get('schoolday.close');
        else
            $close = new \DateTime('1970-01-01 ' .$this->settingManager->get('schoolday.close'));

        foreach ($value as $q => $column) {
            if (empty($column->getStart()))
            {
                if ($this->settingManager->get('schoolday.begin') instanceof \DateTime)
                    $column->setStart($this->settingManager->get('schoolday.begin'));
                else
                    $column->setStart(new \DateTime('1970-01-01 ' . $this->settingManager->get('schoolday.begin')));
            }

            if ($open > $column->getStart()) {
                $this->context->buildViolation($constraint->message . '.open')
                    ->atPath('[' . $q . '].start')
                    ->setParameter('%open%', $open->format('H:i'))
                    ->setTranslationDomain('Timetable')
                    ->addViolation();
            }

            if (empty($column->getEnd()))
            {
                if ($this->settingManager->get('schoolday.finish') instanceof \DateTime)
                    $column->setEnd($this->settingManager->get('schoolday.finish'));
                else
                    $column->setEnd(new \DateTime('1970-01-01 ' . $this->settingManager->get('schoolday.finish')));
            }

            if ($close < $column->getEnd()) {
                $this->context->buildViolation($constraint->message . '.close')
                    ->atPath('[' . $q . '].end')
                    ->setParameter('%close%', $close->format('H:i'))
                    ->setTranslationDomain('Timetable')
                    ->addViolation();
            }

            if ($column->getStart() > $column->getEnd()) {
                $this->context->buildViolation($constraint->message . '.order')
                    ->atPath('[' . $q . '].start')
                    ->setTranslationDomain('Timetable')
                    ->addViolation();
            }
        }
        return;
    }
}