<?php
namespace App\People\Entity;

use App\Entity\Calendar;
use Hillrange\Security\Util\UserTrackInterface;
use Hillrange\Security\Util\UserTrackTrait;

abstract class RollGroupExtension implements UserTrackInterface
{
	use UserTrackTrait;
	/**
	 * @return string
	 */
	public function getCalendarGroupName(): string
	{
		if (!empty($this->getCalendarGroup()))
			return $this->getCalendarGroup()->getFullName();

		return '';
	}

	/**
	 * @return bool
	 */
	public function canDelete(): bool
	{
		return true;
	}

	/**
	 * @return Calendar|null
	 */
	public function getCalendar(): ?Calendar
	{
		if (!empty($this->getCalendarGroup()))
			return $this->getCalendarGroup()->getCalendar();

		return null;
	}

	/**
	 * @return string
	 */
	public function getStudentName(): string
	{
		if (!is_null($this->getStudent()))
			return $this->getStudent()->formatName();

		return '';
	}

	/**
	 * @return int
	 */
	public function getStudentId(): int
	{
		if (!is_null($this->getStudent()))
			return $this->getStudent()->getId();

		return 0;
	}
}