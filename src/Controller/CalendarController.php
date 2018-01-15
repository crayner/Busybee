<?php
namespace App\Controller;

use App\Core\Form\CalendarType;
use App\Core\Manager\CalendarGroupManager;
use App\Core\Manager\CalendarManager;
use App\Core\Manager\FlashBagManager;
use App\Core\Manager\MessageManager;
use App\Entity\Calendar;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CalendarController extends Controller
{
	/**
	 * @Route("/calendar/list/", name="calendar_years")
	 * @IsGranted("ROLE_REGISTRAR")
	 * @return Response
	 */
	public function yearsAction(CalendarManager $calendarManager)
	{
		$calendars = $calendarManager->getCalendarRepository()->findBy([], ['firstDay' => 'DESC']);

		return $this->render('Calendar/calendars.html.twig',
			[
				'Calendars' => $calendars, 'manager' => $calendarManager,
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
	public function edit($id, Request $request, CalendarManager $calendarManager, CalendarGroupManager $calendarGroupManager, EntityManagerInterface $em, MessageManager $messageManager, FlashBagManager $flashBagManager)
	{
		if ($id === 'current')
		{
			$calendar = $calendarManager->getCurrentCalendar();

			return $this->redirectToRoute('calendar_edit', ['id' => $calendar->getId()]);
		}

		$calendar = $id === 'Add' ? new Calendar() : $calendarManager->getCalendarRepository()->find($id);

		$form = $this->createForm(CalendarType::class, $calendar, ['calendarGroupManager' => $calendarGroupManager]);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$em->persist($calendar);
			$em->flush();

			$messageManager->add('success', 'calendar.success', [], 'Calendar');

			$flashBagManager->addMessages($messageManager);

			if ($id === 'Add')
				return new RedirectResponse($this->generateUrl('year_edit', array('id' => $calendar->getId())));

			$id = $calendar->getId();

			$form = $this->createForm(CalendarType::class, $calendar, ['calendarGroupManager' => $calendarGroupManager]);
		}

		return $this->render('Calendar/calendar.html.twig',
			[
				'form'     => $form->createView(),
				'fullForm' => $form,
				'calendar_id'  => $id,
				'manager' => $calendarManager,
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

		$calendar = $repo->find($id);

		$em = $this->get('doctrine')->getManager();
		$em->remove($calendar);
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
			$calendar = $this->get('busybee_core_security.doctrine.user_manager')->getSystemYear($this->getUser());
		}
		else
			$calendar = $repo->find($id);

		$calendars = $repo->findBy([], ['name' => 'ASC']);

		$calendar = $repo->find($calendar->getId());

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

		$calendar->initialiseTerms();

		$calendar = $service->generate($calendar); //Generate a calendar for specified year

		$cm = $this->get('busybee_core_calendar.model.calendar_manager');

		$cm->setCalendarDays($calendar, $calendar);

		/*
         * Pass calendar to Twig
         */

		return $this->render('BusybeeCalendarBundle:Calendar:yearCalendar.html.twig',
			array(
				'calendar'    => $calendar,
				'year'        => $calendar,
				'years'       => $calendars,
				'closeWindow' => $closeWindow,
			)
		);
	}
}