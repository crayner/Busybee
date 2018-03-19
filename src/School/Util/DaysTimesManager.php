<?php
namespace App\School\Util;

use App\Core\Manager\SettingManager;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;

class DaysTimesManager
{
	/**
	 * @var Day
	 */
	private $day;

	/**
	 * @var SchoolTime
	 */
	private $time;

	/**
	 * @var SettingManager
	 */
	private $settingManager;

	/**
	 * HouseManager constructor.
	 */
	public function __construct(SettingManager $settingManager, SchoolTime $time)
	{
		$this->setDay(new Day());
		$this->setTime($time);
		$this->settingManager = $settingManager;
		$this->loadDaysTimes();
	}

	/**
	 * @return DaysTimesManager
	 */
	private function loadDaysTimes(): DaysTimesManager
	{
		$this->time->setOpen($this->settingManager->get('schoolday.open'));

		$this->time->setBegin($this->settingManager->get('schoolday.begin'));

		$this->time->setFinish($this->settingManager->get('schoolday.finish'));

		$this->time->setClose($this->settingManager->get('schoolday.close'));


		foreach ($this->settingManager->get('schoolweek') as $name => $shortName)
		{
			$method = 'set' . ucfirst($shortName);
			$this->day->$method(true);
		}

		return $this;
	}

	/**
	 * @return Day
	 */
	public function getDay(): Day
	{
		return $this->day;
	}

	/**
	 * @param Day $day
	 *
	 * @return DaysTimesManager
	 */
	public function setDay(Day $day): DaysTimesManager
	{
		$this->day = $day;

		return $this;
	}

	/**
	 * @return SchoolTime
	 */
	public function getTime(): SchoolTime
	{
		return $this->time;
	}

	/**
	 * @param SchoolTime $time
	 *
	 * @return DaysTimesManager
	 */
	public function setTime(SchoolTime $time): DaysTimesManager
	{
		$this->time = $time;

		return $this;
	}

	public function saveDaysTimes(FormInterface $form): DaysTimesManager
	{
		$data = $form->getData();

		$days = $data->getDay();

		$w = [];

		if ($days->isSun())
			$w['Sunday'] = 'Sun';
		if ($days->isMon())
			$w['Monday'] = 'Mon';
		if ($days->isTue())
			$w['Tuesday'] = 'Tue';
		if ($days->isWed())
			$w['Wednesday'] = 'Wed';
		if ($days->isThu())
			$w['Thursday'] = 'Thu';
		if ($days->isFri())
			$w['Friday'] = 'Fri';
		if ($days->isSat())
			$w['Saturday'] = 'Sat';


		$this->settingManager->set('schoolweek', $w);

		$this->settingManager->set('schoolday.open', $data->getTime()->getOpen());
		$this->settingManager->set('schoolday.begin', $data->getTime()->getBegin());
		$this->settingManager->set('schoolday.finish', $data->getTime()->getFinish());
		$this->settingManager->set('schoolday.close', $data->getTime()->getClose());

		return $this;
	}
}