<?php
namespace App\School\Validator\Constraints;

use App\School\Util\DaysTimesManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class TimesValidator extends ConstraintValidator
{
	/**
	 * @var DaysTimesManager
	 */
	private $manager;

	/**
	 * HousesValidator constructor.
	 *
	 * @param DaysTimesManager $manager
	 */
	public function __construct(DaysTimesManager $manager)
	{
		$this->manager = $manager;
	}

	/**
	 * @param mixed      $value
	 * @param Constraint $constraint
	 */
	public function validate($value, Constraint $constraint)
	{
		if ($value->getOpen() > $value->getBegin())
			$this->context->buildViolation('school.admin.day_time.open.error')
				->atPath('open')
				->setTranslationDomain('School')
				->addViolation();
		if ($value->getBegin() > $value->getFinish())
			$this->context->buildViolation('school.admin.day_time.begin.error')
				->atPath('begin')
				->setTranslationDomain('School')
				->addViolation();
		if ($value->getFinish() > $value->getClose())
			$this->context->buildViolation('school.admin.day_time.finish.error')
				->atPath('finish')
				->setTranslationDomain('School')
				->addViolation();
	}

}