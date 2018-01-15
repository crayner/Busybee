<?php
namespace App\Core\Manager;

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
	 * @var CalendarRepository
	 */
	private $calendarRepository;

	/**
	 * YearManager constructor.
	 *
	 * @param ObjectManager $manager
	 */
	public function __construct(EntityManagerInterface $manager, TokenStorageInterface $tokenStorage)
	{
		$this->manager = $manager;
		$this->tokenStorage = $tokenStorage;
		$this->calendarRepository = $manager->getRepository(Calendar::class);
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
calendarGroups:
    label: calendar.calendarGroups.tab
    include: Calendar/groups.html.twig
    message: calendarGroupMessage
");
	}
}