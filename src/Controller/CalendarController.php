<?php
namespace App\Controller;

use App\Core\Exception\Exception;
use App\Calendar\Form\CalendarType;
use App\Calendar\Util\RollGroupManager;
use App\Calendar\Util\CalendarManager;
use App\Core\Manager\FlashBagManager;
use App\Core\Manager\MessageManager;
use App\Entity\Calendar;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Html2Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
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
	public function calendars(CalendarManager $calendarManager)
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
	public function edit($id, Request $request, CalendarManager $calendarManager, RollGroupManager $rollGroupManager, EntityManagerInterface $em, MessageManager $messageManager, FlashBagManager $flashBagManager)
	{
		if ($id === 'current')
		{
			$calendar = $calendarManager->getCurrentCalendar();

			return $this->redirectToRoute('calendar_edit', ['id' => $calendar->getId()]);
		}

		$calendar = $id === 'Add' ? new Calendar() : $calendarManager->getCalendarRepository()->find($id);

		$form = $this->createForm(CalendarType::class, $calendar, ['rollGroupManager' => $rollGroupManager]);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$em->persist($calendar);
			$em->flush();

			$messageManager->add('success', 'calendar.success', [], 'Calendar');

			$flashBagManager->addMessages($messageManager);

			if ($id === 'Add')
				return new RedirectResponse($this->generateUrl('calendar_edit', array('id' => $calendar->getId())));

			$id = $calendar->getId();

			$form = $this->createForm(CalendarType::class, $calendar, ['rollGroupManager' => $rollGroupManager]);
		} else
		    $em->refresh($calendar);

		/*
            The calendar must be refreshed as the calendar will be written by the page loader from the cache
            and will error on the write if the database restraints are violated.  The form data is NOT
		    impacted by this refresh.
        */

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
	public function deleteYear($id)
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
	 * @Route("/calendar/display/{id}/{closeWindow}", name="calendar_display")
	 * @IsGranted("ROLE_USER")
	 * @return  Response
	 */
	public function display($id, $closeWindow = false, CalendarManager $calendarManager)
	{
		$repo = $calendarManager->getCalendarRepository();

		if ($id == 'current')
		{
			$calendar = $calendarManager->getCurrentCalendar();
		}
		else
			$calendar = $repo->find($id);

		$calendars = $repo->findBy([], ['name' => 'ASC']);

		$calendar = $repo->find($calendar->getId());

		/**
		 * Set model classes for calendar. Arguments:
		 * 1. For the whole calendar (watch $calendar variable). Default: \TFox\CalendarBundle\Service\WidgetService\Calendar
		 * 2. Month. Default: \TFox\CalendarBundle\Service\WidgetService\Month
		 * 3. Week. Default: '\TFox\CalendarBundle\Service\WidgetService\Week
		 * 4. Day. Default: '\TFox\CalendarBundle\Service\WidgetService\Day'
		 * To set default classes null should be passed as argument
		 */

		$calendar->initialiseTerms();

		$year = $calendarManager->generate($calendar); //Generate a calendar for specified year

		$calendarManager->setCalendarDays($year, $calendar);

		/*
         * Pass calendar to Twig
         */

		return $this->render('Calendar/displayCalendar.html.twig',
			array(
				'calendar'    => $calendar,
				'calendars'   => $calendars,
				'year'        => $year,
				'closeWindow' => $closeWindow,
			)
		);
	}
	/**
	 * @param $id
	 * @Route("/calendar/print/{id}/", name="calendar_print")
	 * @IsGranted("ROLE_USER")
	 * @return Response
	 */
	public function print($id, CalendarManager $calendarManager, EntityManagerInterface $om)
	{
		$calendar = $calendarManager->getCalendarRepository()->find($id);

		if (! empty($calendar->getDownloadCache()) && file_exists($calendar->getDownloadCache()))
			return new JsonResponse(
				[
					'file' => base64_encode($calendar->getDownloadCache()),
				],
				200
			);

		$year =  $calendarManager->generate($calendar); //Generate a calendar for specified year

		$calendarManager->setCalendarDays($year, $calendar);

		/*
         * Pass calendar to Twig
         */
		$content = $this->renderView('Calendar/calendarView.pdf.twig',
			array(
				'calendar' => $calendar,
				'year'     => $year,
			)
		);

		try
		{
			$locale = substr(empty($this->getUser()->getLocale()) ? $this->getParameter('locale') : $this->getUser()->getLocale(), 0, 2);

			$pdf = new Html2Pdf('L', 'A4', $locale);
			ini_set('max_execution_time', 90);
			$pdf->writeHtml($content);

			$pdf_content = $pdf->output('ignore_me.pdf', 'S');

			$cName = 'calendar_' . $calendar->getName();
			$fName = $cName . '_' . mb_substr(md5(uniqid()), mb_strlen($cName) + 1) . '.pdf';

			$path = $this->getParameter('upload_path');

			file_put_contents($path . DIRECTORY_SEPARATOR . $fName, $pdf_content);

			$calendar->setDownloadCache($path . DIRECTORY_SEPARATOR . $fName);

			$om->persist($calendar);
			$om->flush();

			return new JsonResponse(
				[
					'file' => base64_encode($calendar->getDownloadCache()),
				],
				200
			);
		}
		catch (Html2PdfException $e)
		{
			$formatter = new ExceptionFormatter($e);

			throw new Exception($formatter->getHtmlMessage());
		}
	}
}