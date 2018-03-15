<?php
namespace App\Controller;

use App\Calendar\Util\CalendarGradeManager;
use App\Core\Exception\Exception;
use App\Calendar\Form\CalendarType;
use App\Calendar\Util\CalendarManager;
use App\Core\Manager\FlashBagManager;
use App\Core\Manager\MessageManager;
use App\Entity\Calendar;
use App\School\Util\RollGroupManager;
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
     * @param CalendarManager $calendarManager
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
     * @Route("/calendar/edit/{id}/", name="calendar_edit")
     * @IsGranted("ROLE_REGISTRAR")
     * @param $id
     * @param Request $request
     * @param CalendarManager $calendarManager
     * @param EntityManagerInterface $em
     * @param MessageManager $messageManager
     * @param FlashBagManager $flashBagManager
     * @param CalendarGradeManager $calendarGradeManager
     * @return RedirectResponse|Response
     */
	public function edit($id, Request $request,
                         CalendarManager $calendarManager,
                         EntityManagerInterface $em, MessageManager $messageManager,
                         FlashBagManager $flashBagManager, CalendarGradeManager $calendarGradeManager)
	{
		if ($id === 'current')
		{
			$calendar = $calendarManager->getCurrentCalendar();

			return $this->redirectToRoute('calendar_edit', ['id' => $calendar->getId()]);
		}

		$calendar = $id === 'Add' ? new Calendar() : $calendarManager->getCalendarRepository()->find($id);

		$form = $this->createForm(CalendarType::class, $calendar, [ 'calendarGradeManager' => $calendarGradeManager]);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$em->persist($calendar);
			$em->flush();

			$messageManager->add('success', 'calendar.success', [], 'Calendar');

			$flashBagManager->addMessages($messageManager);

			if ($id === 'Add')
				return new RedirectResponse($this->generateUrl('calendar_edit', array('id' => $calendar->getId())));

            $em->refresh($calendar);

            $form = $this->createForm(CalendarType::class, $calendar, ['calendarGradeManager' => $calendarGradeManager]);
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
     * @param CalendarManager $calendarManager
     * @return RedirectResponse
     * @Route("/calendar/delete/{id}/", name="calendar_delete")
     * @IsGranted("ROLE_REGISTRAR")
     */
	public function deleteYear($id, CalendarManager $calendarManager, FlashBagManager $flashBagManager)
	{
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, null);

		$calendar = $calendarManager->find($id);

        if ($calendarManager->canDelete($calendar)) {
            $em = $calendarManager->getEntityManager();
            $em->remove($calendar);
            $em->flush();
            $calendarManager->getMessageManager()->add('success', 'calendar.removal.success', ['%{name}' => $calendar->getName()]);
        } else
            $calendarManager->getMessageManager()->add('warning', 'calendar.removal.denied', ['%{name}' => $calendar->getName()]);

        $flashBagManager->addMessages($calendarManager->getMessageManager());

		return new RedirectResponse($this->generateUrl('calendar_years'));
	}
    /**
     * @param   int $id
     * @param   bool $closeWindow
     * @param CalendarManager $calendarManager
     * @return  Response
     * @Route("/calendar/display/{id}/{closeWindow}", name="calendar_display")
     * @IsGranted("ROLE_USER")
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

		$calendar->getTerms();

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

    /**
     * @Route("/calendar/grade/{id}/manage/{cid}/", name="calendar_grade_manage")
     * @IsGranted("ROLE_REGISTRAR")
     * @param $id
     * @param string $cid
     * @param CalendarManager $calendarManager
     * @param \Twig_Environment $twig
     * @param CalendarGradeManager $calendarGradeManager
     * @return JsonResponse
     */
    public function manageCalendarGrade($id, $cid = 'ignore', CalendarManager $calendarManager, \Twig_Environment $twig, CalendarGradeManager $calendarGradeManager)
    {
        $calendarManager->find($id);

        $calendarManager->removeCalendarGrade($cid);

        $form = $this->createForm(CalendarType::class, $calendarManager->getCalendar(), [ 'calendarGradeManager' => $calendarGradeManager]);

        $collection = $form->has('calendarGrades') ? $form->get('calendarGrades')->createView() : null;

        if (empty($collection)) {
            $calendarManager->getMessageManager()->add('warning', 'calendar.grades.not_defined');
            $calendarManager->setStatus('warning');
        }

        $content = $this->renderView("Calendar/calendar_collection.html.twig",
            [
                'collection'    => $collection,
                'route'         => 'calendar_grade_manage',
                'contentTarget' => 'gradeCollection',
            ]
        );

        return new JsonResponse(
            [
                'content' => $content,
                'message' => $calendarManager->getMessageManager()->renderView($twig),
                'status'  => $calendarManager->getStatus(),
            ],
            200
        );
    }

    /**
     * @Route("/calendar/term/{id}/manage/{cid}/", name="calendar_term_manage")
     * @IsGranted("ROLE_REGISTRAR")
     * @param $id
     * @param string $cid
     * @param CalendarManager $calendarManager
     * @param \Twig_Environment $twig
     * @param CalendarGradeManager $calendarGradeManager
     * @return JsonResponse
     */
    public function manageTerm($id, $cid = 'ignore', CalendarManager $calendarManager, \Twig_Environment $twig, CalendarGradeManager $calendarGradeManager)
    {
        $calendarManager->find($id);

        $calendarManager->removeTerm($cid);

        $form = $this->createForm(CalendarType::class, $calendarManager->getCalendar(), [ 'calendarGradeManager' => $calendarGradeManager]);

        $collection = $form->has('terms') ? $form->get('terms')->createView() : null;

        if (empty($collection)) {
            $calendarManager->getMessageManager()->add('warning', 'calendar.terms.not_defined');
            $calendarManager->setStatus('warning');
        }

        $content = $this->renderView("Calendar/calendar_collection.html.twig",
            [
                'collection'    => $collection,
                'route'         => 'calendar_term_manage',
                'contentTarget' => 'termCollection',
            ]
        );

        return new JsonResponse(
            [
                'content' => $content,
                'message' => $calendarManager->getMessageManager()->renderView($twig),
                'status'  => $calendarManager->getStatus(),
            ],
            200
        );
    }

    /**
     * @Route("/calendar/special/day/{id}/manage/{cid}/", name="calendar_special_day_manage")
     * @IsGranted("ROLE_REGISTRAR")
     * @param $id
     * @param string $cid
     * @param CalendarManager $calendarManager
     * @param \Twig_Environment $twig
     * @param CalendarGradeManager $calendarGradeManager
     * @return JsonResponse
     */
    public function manageSpecialDay($id, $cid = 'ignore', CalendarManager $calendarManager, \Twig_Environment $twig, CalendarGradeManager $calendarGradeManager)
    {
        $calendarManager->find($id);

        $calendarManager->removeSpecialDay($cid);

        $form = $this->createForm(CalendarType::class, $calendarManager->getCalendar(), [ 'calendarGradeManager' => $calendarGradeManager]);

        $collection = $form->has('specialDays') ? $form->get('specialDays')->createView() : null;

        if (empty($collection)) {
            $calendarManager->getMessageManager()->add('warning', 'calendar.special_days.not_defined');
            $calendarManager->setStatus('warning');
        }

        $content = $this->renderView("Calendar/calendar_collection.html.twig",
            [
                'collection'    => $collection,
                'route'         => 'calendar_special_day_manage',
                'contentTarget' => 'specialDayCollection',
            ]
        );

        return new JsonResponse(
            [
                'content' => $content,
                'message' => $calendarManager->getMessageManager()->renderView($twig),
                'status'  => $calendarManager->getStatus(),
            ],
            200
        );
    }
}