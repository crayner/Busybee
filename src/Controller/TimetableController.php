<?php
namespace App\Controller;

use App\Core\Manager\FlashBagManager;
use App\Core\Manager\TwigManager;
use App\Pagination\ClassPagination;
use App\Pagination\LinePagination;
use App\Pagination\PeriodPagination;
use App\Pagination\TimetablePagination;
use App\Security\VoterDetails;
use App\Timetable\Form\ColumnType;
use App\Timetable\Form\TimetableType;
use App\Timetable\Util\ColumnManager;
use App\Timetable\Util\PeriodManager;
use App\Timetable\Util\TimetableDisplayManager;
use App\Timetable\Util\TimetableManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class TimetableController extends Controller
{
    /**
     * @Route("timetable/manage/", name="timetable_manage")
     * @IsGranted("ROLE_PRINCIPAL")
     * @param Request $request
     * @param TimetablePagination $classPagination
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function manage(Request $request, TimetablePagination $classPagination) // was listAction
    {
        $classPagination->injectRequest($request);

        $classPagination->getDataSet();

        return $this->render('Timetable/manage.html.twig',
            [
                'pagination' => $classPagination,
            ]
        );
    }

    /**
     * @Route("/timetable/{id}/edit/", name="timetable_edit")
     * @IsGranted("ROLE_PRINCIPAL")
     * @param Request $request
     * @param $id integer|string
     * @param TimetableManager $timetableManager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(Request $request, $id, TimetableManager $timetableManager)
    {
        $entity = $timetableManager->find($id);

        if (!empty($request->request->get('timetable_days')['locked']) && $request->request->get('timetable_days')['locked'])
            $entity->setLocked(true);

        $form = $this->createForm(TimetableType::class, $entity);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $timetableManager->getEntityManager();

            $em->persist($entity);
            $em->flush();

            if ($id == 'Add')
                return $this->redirectToRoute('timetable_edit', ['id' => $entity->getId()]);

            $form = $this->createForm(TimetableType::class, $entity);
            $timetableManager->getMessageManager()->add('success', 'form.submit.success', [], 'home');
        }

        return $this->render('Timetable/edit.html.twig',
            [
                'form'          => $form->createView(),
                'fullForm'      => $form,
                'tabManager'    => $timetableManager,
            ]
        );
    }

    /**
     * @IsGranted("ROLE_PRINCIPAL")
     * @Route("/timetable/{id}/days/{cid}/edit/", name="timetable_days_edit")
     * @param $id
     * @param string $cid
     * @param TimetableManager $timetableManager
     * @param TwigManager $twig
     * @return JsonResponse
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function editTimetableDays($id, $cid = 'ignore', TimetableManager $timetableManager, TwigManager $twig)
    {
        $entity = $timetableManager->find($id);

        if ($cid !== 'ignore')
            $timetableManager->removeColumn($cid);

        $form = $this->createForm(TimetableType::class, $entity);

        $content = $this->renderView('Timetable/timetable_collection.html.twig',
            [
                'collection' => $form->get('columns')->createView(),
                'tabManager' => $timetableManager,
                'route' => 'timetable_days_edit',
                'contentTarget' => 'columnCollection',
            ]
        );

        return new JsonResponse(
            [
                'content' => $content,
                'status' => $timetableManager->getStatus(),
                'message' => $timetableManager->getMessageManager()->renderView($twig->getTwig()),
            ],
            200
        );
    }

    /**
     * @param   Request $request
     * @param $id
     * @param string $all
     * @param TimetableManager $timetableManager
     * @param PeriodPagination $periodPagination
     * @param LinePagination $linePagination
     * @param ClassPagination $classPagination
     * @param PeriodManager $periodManager
     * @return  \Symfony\Component\HttpFoundation\Response
     * @IsGranted("ROLE_PRINCIPAL")
     * @Route("/timetable/{id}/builder/{all}/", name="timetable_builder")
     */
    public function builder(Request $request, $id, $all = 'All',
                                  TimetableManager $timetableManager, PeriodPagination $periodPagination,
                                  LinePagination $linePagination, ClassPagination $classPagination,
                                  PeriodManager $periodManager)
    {
        $timetable = $timetableManager->find($id);

//	    if ($timetable->getTimetable()->getLocked() && ! $timetable->getTimetable()->getGenerated())
//		    return $this->generateTimetable($id);

        $periodPagination->setTimetable($timetable);

        $periodPagination->injectRequest($request);
        $classPagination->injectRequest($request);
        $linePagination->injectRequest($request);


        $gradeControl = $request->getSession()->get('gradeControl');

        $gradeControl = is_array($gradeControl) ? $gradeControl : [];

        $param = [];
        foreach ($timetableManager->getCalendarGrades() as $q => $w)
        {
            if (isset($gradeControl[$w->getGrade()]) && $gradeControl[$w->getGrade()])
                $param[] = $w->getGrade();
            else
                $gradeControl[$w->getGrade()] = false;
        }

        $request->getSession()->set('gradeControl', $gradeControl);

        $search = [];
        if (!empty($param)) {
            $search['where'] = 'g.grade IN (__name__)';
            $search['parameter'] = $param;
        }

        $classPagination->setLimit(1000)
            ->setJoin([
                'f.course' => [
                    'alias' => 'c',
                    'type' => 'leftJoin',
                ],
                'c.calendarGrades' => [
                    'alias' => 'g',
                    'type' => 'leftJoin',
                ],
            ])
            ->setSortByList(
                [
                    'facetoface.name.sort' =>            [
                        'f.name' => 'ASC',
                        'f.code' => 'ASC',
                    ],
                    'bySequence' => [
                        'g.sequence' => 'ASC',
                        'f.name' => 'ASC',
                        'f.code' => 'ASC',
                    ],
                ]
            )
            ->setSortByName('bySequence')
            ->setDisplaySort(false)
            ->setDisplayChoice(false)
            ->addInjectedSearch($search)
            ->setDisplayResult(false);

        $periodPagination->setLimit(1000)
            ->setDisplaySort(false)
            ->setDisplayChoice(false)
            ->setSearch('')
            ->addInjectedSearch($search)
            ->setDisplayResult(false);

        $linePagination->setDisplaySearch(false)
            ->setDisplaySort(false)
            ->setDisplayChoice(false)
            ->setSearch('')
            ->setLimit(1000)
            ->addInjectedSearch($search)
            ->setDisplayResult(false);

        $classPagination->getDataSet();
        $linePagination->getDataSet();
        $periodPagination->getDataSet();

        $report = $timetableManager->getReport($periodPagination);

        return $this->render('Timetable/builder.html.twig',
            [
                'pagination' => $periodPagination,
                'line_pagination' => $linePagination,
                'class_pagination' => $classPagination,
                'periodManager' => $periodManager,
                'all' => $all,
                'report' => $report,
                'manager' => $timetableManager,
            ]
        );
    }

    /**
     * @IsGranted("ROLE_PRINCIPAL")
     * @Route("/timetable/{id}/column/{cid}/remove/", name="column_remove")
     * @param int $id
     * @param int $cid
     * @param TimetableManager $timetableManager
     * @return RedirectResponse
     */
    public function removeColumn(int $id, int $cid, TimetableManager $timetableManager, FlashBagManager $flashBagManager)
    {
        $timetableManager->find($id, true);

        $timetableManager->removeColumn($cid);

        $flashBagManager->addMessages();

        return $this->redirectToRoute('timetable_edit', ['id' => $id]);
    }

    /**
     * @Route("/timetable/column/{id}/reset/times/", name="column_resettimes")
     * @IsGranted("ROLE_PRINCIPAL")
     * @param $id
     * @param TimetableManager $timetableManager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function resetColumnTimes($id, TimetableManager $timetableManager)
    {
        $tt = $timetableManager->find($id);
        if (! $timetableManager->isValidTimetable()) {
            $timetableManager->getMessageManager()->add('warning', 'column.resettime.missing', [], 'Timetable');
            return $this->forward(TimetableController::class . '::edit', ['id' => $id]);
        }

        $sm     = $timetableManager->getSettingManager();
        $begin  = $sm->get('schoolday.begin');
        $finish = $sm->get('schoolday.finish');

        if ($tt->getColumns()->count() > 0) {
            try {
                foreach ($tt->getColumns() as $column) {
                    $column->setStart($begin);
                    $column->setEnd($finish);
                    $timetableManager->getEntityManager()->persist($column);
                }
                $timetableManager->getEntityManager()->flush();
            } catch (\Exception $e) {
                $timetableManager->getMessageManager()->add('danger', 'column.resettime.error', [], 'Timetable');
                return $this->forward(TimetableController::class . '::edit', ['id' => $id]);
            }
        }

        $timetableManager->getMessageManager()->add('success', 'column.resettime.success', [], 'Timetable');
        return $this->forward(TimetableController::class . '::edit', ['id' => $id]);
    }

    /**
     * @Route("/timetable/period/{id}/{page}/", name="period_remove")
     * @IsGranted("ROLE_PRINCIPAL")
     */
    public function removePeriod($id, $page)
    {
        $this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, null);

        $period = $this->get('period.repository')->find($id);
        $status = 'success';


        if (empty($period)) {
            return new JsonResponse(
                array(
                    'status' => 'error',
                    'message' => '<div class="alert alert-danger fadeAlert">' . $this->get('translator')->trans('period.remove.missing', [], 'BusybeeTimetableBundle') . '</div>',
                ),
                200
            );
        }

        $message = '<div class="alert alert-warning fadeAlert">' . $this->get('translator')->trans('period.remove.locked', [], 'BusybeeTimetableBundle') . '</div>';

        if (!$this->get('period.manager')->canDelete($id))
            return new JsonResponse(
                array(
                    'status' => 'warning',
                    'message' => $message,
                ),
                200
            );

        $om = $this->get('doctrine')->getManager();
        $om->remove($period);
        $om->flush();


        $message = '<div class="alert alert-success fadeAlert">' . $this->get('translator')->trans('period.remove.success', [], 'BusybeeTimetableBundle') . '</div>';

        return new JsonResponse(
            array(
                'page' => $page,
                'status' => $status,
                'message' => $message,
            ),
            200
        );
    }

    /**
     * @Route("/period/{id}/line/{line}/drop/", name="period_drop_line")
     * @IsGranted("ROLE_PRINCIPAL")
     */
    public function addLineToPeriod(int $id, int $line, Request $request, PeriodManager $periodManager, TwigManager $twig)
    {
        $period = $periodManager->find($id);

        $periodManager->addLine($line);

        return new JsonResponse(
            [
                'status' => $periodManager->getMessageManager()->getHighestLevel(),
                'message' => $periodManager->getMessageManager()->renderView($twig->getTwig()),
            ],
            200);
    }

    /**
     * @Route("/period/{id}/activity/{activity}/drop/", name="period_drop_activity")
     * @IsGranted("ROLE_PRINCIPAL")
     */
    public function addActivityToPeriod(int $id, int $activity, PeriodManager $periodManager, TwigManager $twig)
    {
        $period = $periodManager->find($id);

        $periodManager->addActivity($activity);

        return new JsonResponse(
            [
                'status' => $periodManager->getMessageManager()->getHighestLevel(),
                'message' => $periodManager->getMessageManager()->renderView($twig->getTwig()),
            ],
            200)
        ;
    }

    /**
     * @param $id
     * @param $activity
     * @return JsonResponse
     * @Route("/period/{id}/activity/{activity}/remove/", name="period_remove_activity")
     * @IsGranted("ROLE_PRINCIPAL")
     */
    public function removePeriodActivity($id, $activity, PeriodManager $periodManager, TwigManager $twig)
    {
        $periodManager->find($id);

        $periodManager->removeActivity($activity);

        return new JsonResponse(
            [
                'status'    => $periodManager->getMessageManager()->getHighestLevel(),
                'message'   => $periodManager->getMessageManager()->renderView($twig->getTwig()),
            ],
            200
        );
    }

    /**
     * @param $grade
     * @return JsonResponse
     * @Route("/grade/{grade}/{value}/control/", name="grade_control")
     * @IsGranted("ROLE_PRINCIPAL")
     */
    public function gradeControl($grade, $value)
    {
        $session = $this->get('session');

        $gradeControl = $session->get('gradeControl');

        if (!is_array($gradeControl))
            $gradeControl = [];

        if (!isset($gradeControl[$grade]))
            $gradeControl[$grade] = boolval($value);

        $gradeControl[$grade] = $gradeControl[$grade] ? false : true;

        $session->set('gradeControl', $gradeControl);

        return new JsonResponse([], 200);
    }

    /**
     * Set Timetable Grade
     *
     * @param $grade
     * @return JsonResponse
     * @Route("/timetable/{grade}/grade/", name="timetable_grade_set")
     * @IsGranted("ROLE_PRINCIPAL")
     */
    public function setTimetableGrade($grade)
    {
        $sess = $this->get('session');

        $gc = $sess->set('tt_identifier', 'grad' . $grade);

        return new JsonResponse([], 200);
    }

    /**
     * Display Timetable
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/timetable//display/{closeWindow}", name="timetable_display")
     * @Security("is_granted('ROLE_PRINCIPAL')")
     */
    public function display(Request $request, VoterDetails $voterDetails, $closeWindow = '', TimetableDisplayManager $timetableDisplayManager)
    {
        $sess = $request->getSession();

        $fullPage = !empty($closeWindow) ? true : false;

        $identifier = $sess->has('tt_identifier') ? $sess->get('tt_identifier') : $timetableDisplayManager->getTimetableIdentifier($this->getUser());

        $voterDetails->parseIdentifier($identifier);

        $this->denyAccessUnlessGranted('ROLE_SYSTEM_ADMIN', $voterDetails, '');

        return $this->render('Timetable/Display/index.html.twig',
            [
                'manager' => $timetableDisplayManager,
                'fullPage' => $fullPage,
                'headerOff' => $fullPage,
            ]
        );
    }

    /**
     * Set Timetable Space
     *
     * @param $space
     * @return JsonResponse
     * @Route("/space/{space}/set/", name="timetable_space_set")
     * @IsGranted("ROLE_PRINCIPAL")
     */
    public function setTimetableSpace($space)
    {
        $sess = $this->get('session');

        $gc = $sess->set('tt_identifier', 'spac' . $space);

        return new JsonResponse([], 200);
    }

    /**
     * Set Timetable Staff
     *
     * @param $grade
     * @return JsonResponse
     * @Route("/staff/{staff}/set/", name="timetable_staff_set")
     * @IsGranted("ROLE_PRINCIPAL")
     */
    public function setTimetableStaff($staff)
    {
        $sess = $this->get('session');

        $gc = $sess->set('tt_identifier', 'staf' . $staff);

        return new JsonResponse([], 200);
    }

    /**
     * @param $tt
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/timetable/{tt}/line/{id}/periods/search/", name="line_periods_search")
     * @IsGranted("ROLE_PRINCIPAL")
     */
    public function searchPeriods($tt, $id)
    {
        $lgm = $this->get('line.manager');

        $data = $lgm->searchForSuitablePeriods($tt, $id);

        return $this->render('BusybeeTimeTableBundle:Line:search.html.twig',
            [
                'report' => $data,
                'manager' => $lgm,
            ]
        );
    }

    /**
     * Refresh Display TimeTable
     *
     * @param string $displayDate
     * @return JsonResponse
     * @Route("/timetable/display/{displayDate}/refresh/", name="timetable_refresh_display")
     * @IsGranted("ROLE_USER");
     */
    public function refreshDisplay($displayDate, VoterDetails $voterDetails, TimetableDisplayManager $timetableDisplayManager, Request $request)
    {
        $sess = $request->getSession();

        $identifier = $sess->has('tt_identifier') ? $sess->get('tt_identifier') : $timetableDisplayManager->getTimeTableIdentifier($this->getUser());

        $voterDetails->parseIdentifier($identifier);

        $this->denyAccessUnlessGranted('ROLE_SYSTEM_ADMIN', $voterDetails, '');

        if ($this->getUser())
            $timetableDisplayManager->generateTimeTable($identifier, $displayDate);

        $content = $this->renderView('Timetable/Display/timetable.html.twig',
            [
                'manager' => $timetableDisplayManager,
            ]
        );

        return new JsonResponse(
            [
                'content'     => $content,
                'description' => $timetableDisplayManager->getDescription(true),
            ],
            200
        );
    }

    /**
     * @IsGranted("ROLE_PRINCIPAL")
     * @Route("/line/builder/", name="timetable_builder_line_activity")
     * @param Request $request
     * @param LinePagination $linePagination
     * @return JsonResponse
     */
    public function builderLineActivity(Request $request,
                                  LinePagination $linePagination)    {

        $linePagination->injectRequest($request);

        $gradeControl = $request->getSession()->get('gradeControl');

        $param = [];
        if (is_array($gradeControl)) {
            foreach ($gradeControl as $q => $w)
                if ($w)
                    $param[] = $q;
        }

        $search = [];
        if (!empty($param)) {
            $search['where'] = 'g.grade IN (__name__)';
            $search['parameter'] = $param;
        }

        $linePagination->setDisplaySearch(false)
            ->setDisplaySort(false)
            ->setDisplayChoice(false)
            ->setSearch('')
            ->setLimit(1000)
            ->addInjectedSearch($search)
            ->setDisplayResult(false);

        $linePagination->getDataSet();

        $content = $this->renderView('Timetable/builder_line_activity.html.twig',
            [
                'line_pagination' => $linePagination,
            ]
        );

        return new JsonResponse(
            [
                'content' => $content,
            ],
            200
        );
    }

    /**
     * @IsGranted("ROLE_PRINCIPAL")
     * @Route("/timetable/{id}/activity/builder/", name="timetable_builder_activity")
     * @param Request $request
     * @param $id
     * @param TimetableManager $timetableManager
     * @param ClassPagination $classPagination
     * @return JsonResponse
     */
    public function builderActivity(Request $request, $id,
                            TimetableManager $timetableManager, ClassPagination $classPagination) {

        $timetableManager->find($id);

        $classPagination->injectRequest($request);

        $gradeControl = $request->getSession()->get('gradeControl');

        $gradeControl = is_array($gradeControl) ? $gradeControl : [];

        $param = [];
        foreach ($timetableManager->getCalendarGrades() as $q => $w)
        {
            if (isset($gradeControl[$w->getGrade()]) && $gradeControl[$w->getGrade()])
                $param[] = $w->getGrade();
            else
                $gradeControl[$w->getGrade()] = false;
        }

        $request->getSession()->set('gradeControl', $gradeControl);

        $search = [];
        if (!empty($param)) {
            $search['where'] = 'g.grade IN (__name__)';
            $search['parameter'] = $param;
        }

        $classPagination->setLimit(1000)
            ->setJoin([
                'f.course' => [
                    'alias' => 'c',
                    'type' => 'leftJoin',
                ],
                'c.calendarGrades' => [
                    'alias' => 'g',
                    'type' => 'leftJoin',
                ],
            ])
            ->setSortByList(
                [
                    'facetoface.name.sort' =>            [
                        'f.name' => 'ASC',
                        'f.code' => 'ASC',
                    ],
                    'bySequence' => [
                        'g.sequence' => 'ASC',
                        'f.name' => 'ASC',
                        'f.code' => 'ASC',
                    ],
                ]
            )
            ->setSortByName('bySequence')
            ->setDisplaySort(false)
            ->setDisplayChoice(false)
            ->addInjectedSearch($search)
            ->setDisplayResult(false);


        $classPagination->getDataSet();

        $content = $this->renderView('Timetable/builder_activity_list.html.twig',
            [
                'class_pagination' => $classPagination,
            ]
        );

        return new JsonResponse(
            [
                'content' => $content,
            ],
            200
        );
    }

    /**
     * @Route("/timetable/{id}/assigned/days/", name="timetable_assign_days")
     * @IsGranted("ROLE_PRINCIPAL")
     * @param $id
     * @param TimetableManager $timetableManager
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function generateAssignedDays($id, TimetableManager $timetableManager)
    {
        $timetableManager->createAssignedDays($id);

        return $this->forward(TimetableController::class.'::edit', ['id' => $id]);
    }

    /**
     * @Route("/timetable/{id}/start/{date}/rotate/", name="timetable_day_rotate_toggle")
     * @IsGranted("ROLE_PRINCIPAL")
     * @param $date
     * @param $id
     * @param TimetableManager $timetableManager
     * @param TwigManager $twig
     * @return JsonResponse
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws \Exception
     */
    public function rotateToggle($date, $id, TimetableManager $timetableManager, TwigManager $twig)
    {
        $timetableManager->find($id);
        $timetableManager->getMessageManager()->setDomain('Timetable');

        if (!$timetableManager->testDate($date)) {
            return new JsonResponse(
                [
                    'message' => $timetableManager->getMessageManager()->renderView($twig->getTwig()),
                    'status' => 'failed'
                ],
                200
            );
        }

        $day = $timetableManager->toggleRotateStart($date);

        $data = $this->renderView('Timetable/Day/assign_days_content.html.twig', [
            'term' => $day->getTerm(),
            'tabManager' => $timetableManager,
        ]);

        return new JsonResponse(
            [
                'message' => $timetableManager->getMessageManager()->renderView($twig->getTwig()),
                'data' => $data,
                'status' => 'success',
            ],
            200
        );
    }

    /**
     * @Route("/column/{id}/periods/manage/", name="column_periods_manage")
     * @IsGranted("ROLE_PRINCIPAL")
     * @param $id
     * @param ColumnManager $columnManager
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function managePeriodsInColumn($id, ColumnManager $columnManager, Request $request)
    {
        $column = $columnManager->find($id);

        $columnManager->generatePeriods();

        $form = $this->createForm(ColumnType::class, $columnManager->getColumn());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            foreach($columnManager->getColumn()->getPeriods()->getIterator() as $period)
                $columnManager->getEntityManager()->persist($period);
            $columnManager->getEntityManager()->persist($columnManager->getColumn());
            $columnManager->getEntityManager()->flush();

            $form = $this->createForm(ColumnType::class, $columnManager->getColumn());
        }

        return $this->render('Timetable/Period/manage.html.twig',
            [
                'form' => $form->createView(),
                'fullForm' => $form,
            ]
        );
    }

    /**
     * @param Request $request
     * @param $activity
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/period/activity/{activity}/edit/", name="period_activity_edit")
     * @IsGranted("ROLE_PRINCIPAL")
     */
    public function editPeriodActivity(Request $request, $activity)
    {
        $act = $this->get('period.activity.repository')->find($activity);

        $year = $this->get('busybee_core_calendar.model.get_current_year');

        $act->setLocal(true);

        $form = $this->createForm(EditPeriodActivityType::class, $act, ['year_data' => $year]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $om = $this->get('doctrine')->getManager();
            $om->persist($act);
            $om->flush();
        }

        return $this->render('BusybeeTimeTableBundle:Periods:activity.html.twig',
            [
                'form' => $form->createView(),
                'fullForm' => $form,
            ]
        );
    }

    /**
     * @IsGranted("ROLE_PRINCIPAL")
     * @Route("/timetable/{id}/period/builder/{all}/", name="timetable_period_builder")
     * @param Request $request
     * @param $id
     * @param string $all
     * @param TimetableManager $timetableManager
     * @param PeriodPagination $periodPagination
     * @param PeriodManager $periodManager
     * @return JsonResponse
     */
    public function builderPeriod(Request $request, $id, $all = 'All',
                            TimetableManager $timetableManager, PeriodPagination $periodPagination,
                            PeriodManager $periodManager)
    {
        $timetable = $timetableManager->find($id);

        $periodPagination->setTimetable($timetable);

        $periodPagination->injectRequest($request);

        $gradeControl = $request->getSession()->get('gradeControl');

        $gradeControl = is_array($gradeControl) ? $gradeControl : [];

        $param = [];
        foreach ($timetableManager->getCalendarGrades() as $q => $w)
        {
            if (isset($gradeControl[$w->getGrade()]) && $gradeControl[$w->getGrade()])
                $param[] = $w->getGrade();
            else
                $gradeControl[$w->getGrade()] = false;
        }

        $request->getSession()->set('gradeControl', $gradeControl);

        $search = [];
        if (!empty($param)) {
            $search['where'] = 'g.grade IN (__name__)';
            $search['parameter'] = $param;
        }

        $periodPagination->setLimit(1000)
            ->setDisplaySort(false)
            ->setDisplayChoice(false)
            ->setSearch('')
            ->addInjectedSearch($search)
            ->setDisplayResult(false);

        $periodPagination->getDataSet();

        $report = $timetableManager->getReport($periodPagination);

        $content = $this->renderView('Timetable/Period/builder.html.twig',
            [
                'pagination' => $periodPagination,
                'periodManager' => $periodManager,
                'all' => $all,
                'report' => $report,
                'manager' => $timetableManager,
            ]
        );

        return new JsonResponse(
            [
                'content' => $content,
            ],
            200);
    }

    /**
     * @param int $id
     * @param PeriodManager $periodManager
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/period/{id}/report/", name="period_plan_report")
     * @IsGranted("ROLE_PRINCIPAL")
     */
    public function periodReport(int $id, PeriodManager $periodManager)
    {
        $periodManager->find($id);
        $report = $periodManager->clearResults()->generateFullPeriodReport();

        return $this->render('Timetable/Period/report.html.twig',
            [
                'report' => $report,
                'manager' => $periodManager,
            ]
        );
    }
}