<?php
namespace App\Calendar\Util;

use App\Entity\Calendar;
use App\Repository\CalendarRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\DBAL\Driver\PDOException;
use Doctrine\DBAL\Exception\DriverException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Yaml\Yaml;

class CalendarManager
{
	/**
	 * @var EntityManagerInterface
	 */
	private $manager;

	/**
	 * @var Form
	 */
	private $form;

	/**
	 * @var array
	 */
	private $data;

	/**
	 * @var TokenStorageInterface
	 */
	private $tokenStorage;

	/**
	 * @var UserInterface
	 */
	private $currentUser;

    /**
     * @var Calendar
     */
    private $currentCalendar;

    /**
     * @var Calendar
     */
    private $nextCalendar;

	/**
	 * @var CalendarRepository
	 */
	private $calendarRepository;

	/**
	 * @var Year
	 */
	private $year;

	/**
	 * @var  Calendar
	 */
	private $calendar;

	/**
	 * YearManager constructor.
	 *
	 * @param ObjectManager $manager
	 */
	public function __construct(EntityManagerInterface $manager, TokenStorageInterface $tokenStorage, Year $year)
	{
		$this->manager = $manager;
		$this->tokenStorage = $tokenStorage;
		$this->calendarRepository = $manager->getRepository(Calendar::class);
		$this->year = $year;
	}

	/**
	 * @return EntityManagerInterface
	 */
	public function getEntityManager(): EntityManagerInterface
	{
		return $this->manager;
	}

	/**
	 * @param FormEvent $event
	 */
	public function preSubmit(FormEvent $event)
	{
		$this->data = $event->getData();
		$this->form = $event->getForm();

		$year = $this->form->getData();

		if (isset($this->data['terms']) && is_array($this->data['terms']))
		{
			foreach ($this->data['terms'] as $q => $w)
			{
				$w['year']               = $year->getId();
				$this->data['terms'][$q] = $w;
			}
		}

		if (isset($this->data['specialDays']) && is_array($this->data['specialDays']))
		{
			foreach ($this->data['specialDays'] as $q => $w)
			{
				$w['year']                     = $year->getId();
				$this->data['specialDays'][$q] = $w;
			}
		}

		$event->setData($this->data);

		return $event;

	}

	/**
	 * @param $sql
	 * @param $rsm
	 */
	private function executeQuery($sql, $rsm)
	{

		$query = $this->manager->createNativeQuery($sql, $rsm);
		try
		{
			$query->execute();
		}
		catch (PDOException $e)
		{
			if (!in_array($e->getErrorCode(), ['1146']))
				throw new \Exception($e->getMessage());
		}
		catch (DriverException $e)
		{
			if (!in_array($e->getErrorCode(), ['1091']))
				throw new \Exception($e->getMessage());
		}

	}

	/**
	 * Can Delete
	 *
	 * @param Calendar $year
	 *
	 * @return bool
	 */
	public function canDelete(Calendar $calendar)
	{
		if (! $calendar->canDelete())
			return false;

		return true;
	}

	/**
	 *
	 */
	private function getCurrentUser()
	{
		if (! is_null($this->currentUser))
			return ;
		$token = $this->tokenStorage->getToken();

		if (is_null($token))
			return ;

		$user = $token->getUser();
		if ($user instanceof UserInterface)
			$this->currentUser = $user;

		return;
	}

	/**
	 * @return Calendar
	 */
	public function getCurrentCalendar(): Calendar
	{
		if (! is_null($this->currentCalendar))
			return $this->currentCalendar;

		$this->getCurrentUser();
		if ($this->currentUser instanceof UserInterface)
		{
			$settings = $this->currentUser->getUserSettings();
			if (isset($settings['calendar']))
				$this->currentCalendar = $this->calendarRepository->findOneBy(['id' => $settings['calendar']]);
			else
				$this->currentCalendar = $this->calendarRepository->findOneBy(['status' => 'current']);
		}
		else
			$this->currentCalendar = $this->calendarRepository->findOneBy(['status' => 'current']);

		return $this->currentCalendar;
	}

	/**
	 * @return CalendarRepository
	 */
	public function getCalendarRepository(): CalendarRepository
	{
		return $this->calendarRepository;
	}

	/**
	 * @return array
	 */
	public function getTabs(): array
	{
		return Yaml::parse("
calendar:
    label: calendar.calendar.tab
    include: Calendar/calendarTab.html.twig
    message: calendarMessage
terms:
    label: calendar.terms.tab
    include: Calendar/terms.html.twig
    message: termMessage
specialDays:
    label: calendar.specialDays.tab
    include: Calendar/specialDays.html.twig
    message: specialDayMessage
rollGroups:
    label: calendar.roll_groups.tab
    include: Calendar/roll_groups.html.twig
    message: rollGroupMessage
");
	}

	/**
	 * @param Calendar $calendar
	 *
	 * @return Year
	 */
	public function generate(Calendar $calendar)
	{
		$this->calendar = $calendar;
		return $this->year->generate($this->calendar);
	}

	/**
	 * Set Calendar Day Types
	 * 
	 * @param Year     $year
	 * @param Calendar $calendar
	 *
	 * @return Calendar
	 */
	public function setCalendarDays(Year $year, Calendar $calendar)
	{
		$this->year     = $year;
		$this->calendar = $calendar;
		$this->setNonSchoolDays();
		$this->setTermBreaks();
		$this->setClosedDays();
		$this->setSpecialDays();

		return $this->calendar;
	}

	/**
	 * Set Non School Days
	 */
	public function setNonSchoolDays()
	{
		$schoolDays = $this->year->getSettingManager()->get('schoolweek');

		foreach ($this->year->getMonths() as $monthKey => $month)
		{
			foreach ($month->getWeeks() as $weekKey => $week)
			{
				foreach ($week->getDays() as $dayKey => $day)
				{
					// School Day ?
					if (!in_array($day->getDate()->format('D'), $schoolDays))
						$day->setSchoolDay(false);
					else
						$day->setSchoolDay(true);
				}
				$month->getWeeks()[$weekKey] = $week;
			}
			$this->year->getMonths()[$monthKey] = $month;
		}
	}

	/**
	 * Set Term Breaks
	 */
	public function setTermBreaks()
	{
		foreach ($this->year->getMonths() as $monthKey => $month)
		{
			foreach ($month->getWeeks() as $weekKey => $week)
			{
				foreach ($week->getDays() as $dayKey => $day)
				{
					// School Day ?
					$break = $this->isTermBreak($day);
					$this->year->getDay($day->getDate()->format('d.m.Y'))->setTermBreak($break);
					$day->setTermBreak($break);
					$week->getDays()[$dayKey] = $day;
				}
				$month->getWeeks()[$weekKey] = $week;
			}
			$this->year->getMonths()[$monthKey] = $month;
		}
	}

	/**
	 * @param Day $currentDate
	 *
	 * @return bool
	 */
	public function isTermBreak(Day $currentDate)
	{
		// Check if the day is a possible school day. i.e. Ignore Weekends
		if ($currentDate->isTermBreak()) return true;

		foreach ($this->calendar->getTerms() as $term)
		{
			if ($currentDate->getDate() >= $term->getFirstDay() && $currentDate->getDate() <= $term->getLastDay())
				return false;
		}

		$currentDate->setTermBreak(true);

		return true;
	}

	/**
	 *
	 */
	public function setClosedDays()
	{
		if (!is_null($this->calendar->getSpecialDays()))
			foreach ($this->calendar->getSpecialDays() as $specialDay)
				if ($specialDay->getType() == 'closure')
					$this->year->getDay($specialDay->getDay()->format('d.m.Y'))->setClosed(true, $specialDay->getName());
	}

	/**
	 *
	 */
	public function setSpecialDays()
	{
		if (!is_null($this->calendar->getSpecialDays()))
			foreach ($this->calendar->getSpecialDays() as $specialDay)
				if ($specialDay->getType() != 'closure')
					$this->year->getDay($specialDay->getDay()->format('d.m.Y'))->setSpecial(true, $specialDay->getName());
	}

	/**
	 * @param Day  $day
	 * @param null $class
	 *
	 * @return string
	 */
	public function getDayClass(Day $day, $class = null)
	{

		$class    = empty($class) ? '' : $class;
		$weekDays = $this->year->getSettingManager()->get('schoolWeek');
		$weekEnd  = true;

		if (isset($weekDays[$day->getDate()->format('D')]))
			$weekEnd = false;
		if (!$weekEnd)
			$class .= ' dayBold';

		if ($this->isTermBreak($day))
			$class .= ' termBreak';
		if ($day->isClosed())
		{
			$class .= ' isClosed';
			$class = str_replace(' termBreak', '', $class);
		}
		if ($day->isSpecial())
		{
			$class .= ' isSpecial';
			$class = str_replace(' termBreak', '', $class);
		}

		if (!$day->isSchoolDay())
		{
			$class .= ' isNonSchoolDay';
			$class = str_replace(' termBreak', '', $class);
		}

		if (empty($class)) return '';

		return ' class="' . trim($class) . '"';
	}

	/**
	 * @return array
	 */
	public function getCalendarList()
	{
		$results = $this->getEntityManager()->getRepository(Calendar::class)->findBy([], ['firstDay' => 'ASC']);
		return empty($results) ? [] : $results ;
	}

    /**
     * @return Calendar
     */
    public function getNextCalendar()
    {
        if ($this->nextCalendar)
            return $this->nextCalendar;

        $this->getCurrentCalendar();

        $result = $this->getCalendarRepository()->createQueryBuilder('c')
            ->where('c.firstDay > :firstDay')
            ->setParameter('firstDay', $this->getCurrentCalendar()->getFirstDay()->format('Y-m-d'))
            ->getQuery()
            ->getResult();

        $this->nextCalendar = $result ? $result[0] : null ;
        dump($this);
        return $this->nextCalendar;
    }
}