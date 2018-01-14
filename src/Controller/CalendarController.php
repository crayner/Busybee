<?php
namespace App\Controller;

use App\Core\Manager\CalendarManager;
use App\Repository\CalendarRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CalendarController extends Controller
{
	/**
	 * @Route("/calendar/list/", name="calendar_years")
	 * @IsGranted("ROLE_REGISTRAR")
	 * @return Response
	 */
	public function yearsAction(CalendarRepository $calendarRepository, CalendarManager $calendarManager)
	{
		$years = $calendarRepository->findBy([], ['firstDay' => 'DESC']);

		return $this->render('Calendar/calendars.html.twig',
			[
				'Calendars' => $years, 'manager' => $calendarManager,
			]
		);
	}
	/**
	 * @param         $id
	 * @param Request $request
	 * @Route("/calendar/edit/{id}/", name="calendar_edit")
	 * @IsGranted("ROLE_REGISTRAR")
	 * @return RedirectResponse|Response
	 */
	public function editYearAction($id, Request $request)
	{
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, null);

		if ($id === 'current')
		{
			$year = $this->get('busybee_core_calendar.model.get_current_year');

			return new RedirectResponse($this->generateUrl('year_edit', array('id' => $year->getId())));
		}

		$year = $id === 'Add' ? new Year() : $this->get('busybee_core_calendar.repository.year_repository')->find($id);

		$form = $this->createForm(YearType::class, $year, ['calendarGroupManager' => $this->get('busybee_core_calendar.model.calendar_group_manager')]);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$em = $this->get('doctrine')->getManager();
			$em->getConnection()->exec('SET FOREIGN_KEY_CHECKS = 0');
			$em->persist($year);

			$em->flush();
			$em->getConnection()->exec('SET FOREIGN_KEY_CHECKS = 1');

			$request->getSession()
				->getFlashBag()
				->add('success', 'calendar.success');
			if ($id === 'Add')
				return new RedirectResponse($this->generateUrl('year_edit', array('id' => $year->getId())));

			$id = $year->getId();

			$form = $this->createForm(YearType::class, $year, ['calendarGroupManager' => $this->get('busybee_core_calendar.model.calendar_group_manager')]);
		}

		return $this->render('BusybeeCalendarBundle:Calendar:calendar.html.twig',
			[
				'form'     => $form->createView(),
				'fullForm' => $form,
				'id'       => $id,
				'year_id'  => $id,
			]
		);
	}

	/**
	 * @param $id
	 * @Route("/calendar/delete/{id}/", name="calendar_delete")
	 * @IsGranted("ROLE_REGISTRAR")
	 * @return RedirectResponse
	 */
	public function deleteYearAction($id)
	{
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, null);

		$repo = $this->get('busybee_core_calendar.repository.year_repository');

		$year = $repo->find($id);

		$em = $this->get('doctrine')->getManager();
		$em->remove($year);
		$em->flush();

		return new RedirectResponse($this->generateUrl('calendar_years'));
	}

	/**
	 * @param   int  $id
	 * @param   bool $closeWindow
	 * @Route("/calendar/display/{id}/", name="calendar_display")
	 * @IsGranted("ROLE_USER")
	 * @return  Response
	 */
	public function calendarAction($id, $closeWindow = null)
	{
		$this->denyAccessUnlessGranted('ROLE_USER', null, null);

		$repo = $this->get('busybee_core_calendar.repository.year_repository');

		if ($id == 'current')
		{
			$year = $this->get('busybee_core_security.doctrine.user_manager')->getSystemYear($this->getUser());
		}
		else
			$year = $repo->find($id);

		$years = $repo->findBy([], ['name' => 'ASC']);

		$year = $repo->find($year->getId());

		$service = $this->get('busybee_core_calendar.service.widget_service.calendar'); //calling a calendar service

		//Defining a custom classes for rendering of months and days
		$dayModelClass   = Day::class;

		/**
		 * Set model classes for calendar. Arguments:
		 * 1. For the whole calendar (watch $calendar variable). Default: \TFox\CalendarBundle\Service\WidgetService\Calendar
		 * 2. Month. Default: \TFox\CalendarBundle\Service\WidgetService\Month
		 * 3. Week. Default: '\TFox\CalendarBundle\Service\WidgetService\Week
		 * 4. Day. Default: '\TFox\CalendarBundle\Service\WidgetService\Day'
		 * To set default classes null should be passed as argument
		 */
		$service->setModels(null, null, $dayModelClass);

		$year->initialiseTerms();

		$calendar = $service->generate($year); //Generate a calendar for specified year

		$cm = $this->get('busybee_core_calendar.model.calendar_manager');

		$cm->setCalendarDays($year, $calendar);

		/*
         * Pass calendar to Twig
         */

		return $this->render('BusybeeCalendarBundle:Calendar:yearCalendar.html.twig',
			array(
				'calendar'    => $calendar,
				'year'        => $year,
				'years'       => $years,
				'closeWindow' => $closeWindow,
			)
		);
	}
}