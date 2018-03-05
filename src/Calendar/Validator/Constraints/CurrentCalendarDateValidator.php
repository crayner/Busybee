<?php
namespace App\Calendar\Validator\Constraints;

use App\Calendar\Util\CalendarManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use DateTime;

class CurrentCalendarDateValidator extends ConstraintValidator
{
    /**
     * @var CalendarManager
     */
	private $calendarManager;

    /**
     * CurrentCalendarDateValidator constructor.
     * @param CalendarManager $calendarManager
     */
    public function __construct(CalendarManager $calendarManager)
	{
		$this->calendarManager = $calendarManager;
	}

	public function validate($value, Constraint $constraint)
	{
		if (empty($value))
			return;

		$calendar = $this->calendarManager->getCurrentCalendar();

		if ($value instanceof DateTime) {
            if ($value->getTimestamp() < $calendar->getFirstDay()->getTimestamp())
                $this->context->buildViolation($constraint->message)
                    ->setTranslationDomain('Calendar')
                    ->addViolation();
            if ($value->getTimestamp() > $calendar->getLastDay()->getTimestamp())
                $this->context->buildViolation($constraint->message)
                    ->setTranslationDomain('Calendar')
                    ->addViolation();
        } else
            $this->context->buildViolation('calendar.current.validate.date.invalid_format')
                ->setTranslationDomain('Calendar')
                ->addViolation();


	}
}