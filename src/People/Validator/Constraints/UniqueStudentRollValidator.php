<?php
namespace App\People\Validator\Constraints;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueStudentRollValidator extends ConstraintValidator
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * UniqueStudentRollValidator constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
	{
dump($value);
		if (empty($value))
			return;


		return ;
		$current = 0;
		$year    = [];

		foreach ($value->toArray() as $q => $group)
		{
			if (empty($group->getStudent()) || empty($group->getCalendarGroup()))
			{

				$this->context->buildViolation('student.grades.error.empty')
					->setTranslationDomain('Person')
					->atPath('[' . strval($q) . ']')
					->atPath('calendarGroup')
					->addViolation();
			}

			if ($group->getStatus() === 'Current')
			{
				$current++;

				if ($current > 1)
				{
					$this->context->buildViolation('student.grades.error.current')
						->atPath('[' . strval($q) . ']')
						->atPath('status')
						->setTranslationDomain('Person')
						->addViolation();
				}
			}

			$gy = $group->getCalendar()->getName();

			if (!is_null($gy))
			{
				$year[$gy] = !isset($year[$gy]) ? 1 : $year[$gy] + 1;

				if ($year[$gy] > 1)
				{
					$this->context->buildViolation('student.grades.error.year')
						->atPath('[' . strval($q) . ']')
						->atPath('calendarGroup')
						->setTranslationDomain('Person')
						->addViolation();
				}
			}
		}
	}
}