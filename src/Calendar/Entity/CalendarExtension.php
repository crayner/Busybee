<?php
namespace App\Calendar\Entity;

use App\Entity\Calendar;
use Hillrange\Security\Util\UserTrackInterface;
use Hillrange\Security\Util\UserTrackTrait;

abstract class CalendarExtension implements UserTrackInterface
{
	use UserTrackTrait;

	/**
	 * Can Delete
	 *
	 * @return bool
	 */
	public function canDelete()
	{
		if (!empty($this->getTerms()))
			foreach ($this->getTerms()->toArray() as $term)
				if (!$term->canDelete())
					return false;
		if (!empty($this->getCalendarGrades()))
			foreach ($this->getCalendarGrades()->toArray() as $grade)
				if (!$grade->canDelete())
					return false;
		if (!empty($this->getSpecialDays()))
			foreach ($this->getSpecialDays()->toArray() as $specialDay)
				if (!$specialDay->canDelete())
					return false;
		if (!empty($this->getTerms()) && !empty($this->getCalendarGrades()) && !empty($this->getSpecialDays()))
			return false;

		return true;
	}

	/**
	 * @param Calendar $calendar
	 *
	 * @return bool
	 */
	public function isEqual(Calendar $calendar): bool
	{
		if ($calendar->getId() === $this->getId())
			return true;
		return false;
	}
}