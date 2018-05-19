<?php
namespace App\Controller;

use App\Core\Manager\FlashBagManager;
use App\Core\Manager\TwigManager;
use App\Pagination\ActivityPagination;
use App\Pagination\LinePagination;
use App\Pagination\PeriodPagination;
use App\Pagination\TimetablePagination;
use App\Security\VoterDetails;
use App\Timetable\Form\ColumnType;
use App\Timetable\Form\LineType;
use App\Timetable\Form\TimetableType;
use App\Timetable\Util\ColumnManager;
use App\Timetable\Util\LineManager;
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
use Symfony\Component\Security\Core\User\UserInterface;

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
    public function edit($id, Request $request, TimetableManager $timetableManager)
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
     * builder
     *
     * @IsGranted("ROLE_PRINCIPAL")
     * @Route("/timetable/{id}/builder/{all}/", name="timetable_builder")
     * @param $id
     * @param string $all
     * @param TimetableManager $timetableManager
     * @param PeriodPagination $periodPagination
     * @param Request $request
     * @param ActivityPagination $classPagination
     * @param LinePagination $linePagination
     * @param PeriodManager $periodManager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function builder($id, $all = 'All', TimetableManager $timetableManager, PeriodPagination $periodPagination,
                               Request $request, ActivityPagination $classPagination, LinePagination $linePagination,
                               PeriodManager $periodManager)
    {
        $timetable = $timetableManager->find($id);

        $periodPagination->setTimetable($timetable);

        $periodPagination->injectRequest($request);
        $classPagination->injectRequest($request);
        $linePagination->injectRequest($request);

        $grades = $timetableManager->gradeControl();

        $classPagination->setCalendarGrades($grades);
        $linePagination->setCalendarGrades($grades);

        $periodPagination->setLimit(1000)
            ->setDisplaySort(false)
            ->setDisplayChoice(false)
            ->setSearch('')
            ->setDisplayResult(false);

        $linePagination->setDisplaySearch(false)
            ->setDisplaySort(false)
            ->setDisplayChoice(false)
            ->setSearch('')
            ->setLimit(1000)
            ->setDisplayResult(false);

        $classPagination->setActivityType(
            [
                'class',
                'roll',
            ]
        );

        $classPagination->setLimit(1000)
            ->setSortByList(
                [
                    'activity.sort.name' =>            [
                        'a.name' => 'ASC',
                        'a.code' => 'ASC',
                    ],
                    'bySequence' => [
                        'cg.sequence' => 'ASC',
                        'a.name' => 'ASC',
                        'a.code' => 'ASC',
                    ],
                ]
            )
            ->setSortByName('bySequence')
            ->setDisplaySort(false)
            ->setDisplayChoice(false)
            ->setDisplayResult(false);

        $classPagination->getDataSet();
        $linePagination->getDataSet();
        $periodPagination->getDataSet();

        return $this->render('Timetable/builder.html.twig',
            [
                'pagination' => $periodPagination,
                'line_pagination' => $linePagination,
                'class_pagination' => $classPagination,
                'periodManager' => $periodManager,
                'all' => $all,
                'report' => $timetableManager->getReport($periodPagination),
                'manager' => $timetableManager,
            ]
        );
    }

    /**
     * @Route("/line/list/", name="line_list")
     * @IsGranted("ROLE_PRINCIPAL")
     * @param Request $request
     * @param LinePagination $linePagination
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function lineList(Request $request, LinePagination $linePagination)
    {
        $linePagination->injectRequest($request);

        $linePagination->getDataSet();

        return $this->render('Timetable/Line/list.html.twig',
            [
                'pagination' => $linePagination,
            ]
        );
    }

    /**
     * @param Request $request
     * @param int|string $id
     * @param null $closeWindow
     * @param LineManager $lineManager
     * @Route("/line/{id}/manage/{closeWindow}", name="line_manage")
     * @IsGranted("ROLE_PRINCIPAL")
     */
    public function lineManage(Request $request, $id = 'Add', $closeWindow = '', LineManager $lineManager)
    {
        $entity = $lineManager->find($id);

        $form = $this->createForm(LineType::class, $entity, ['calendar_data' => $lineManager->getCalendar()]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $lineManager->getEntityManager();
            foreach($entity->getActivities()->getIterator() as $activity)
                $em->persist($activity->setLine($entity));
            $em->persist($entity);
            $em->flush();

            if ($id == 'Add') {
                $close = [];
                if (!empty($closeWindow))
                    $close = ['closeWindow' => '_closeWindow'];

                return $this->redirectToRoute('line_manage', array_merge(['id' => $entity->getId()], $close));
            }
        }

        return $this->render('Timetable/Line/manage.html.twig',
            [
                'form' => $form->createView(),
                'fullForm' => $form,
            ]
        );
    }

    /**
     * @param $id
     * @param LineManager $lineManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/line/{id}/delete/", name="line_delete")
     * @IsGranted("ROLE_PRINCIPAL")
     */
    public function lineDelete($id, LineManager $lineManager)
    {
        $lineManager->deleteLine($id);

        return $this->redirectToRoute('line_list');
    }

    /**
     * @param $id
     * @param $cid
     * @param LineManager $lineManager
     * @param TwigManager $twig
     * @return JsonResponse
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @Route("/line/{id}/activity/{cid}/remove/", name="line_remove_activity")
     * @IsGranted("ROLE_PRINCIPAL")
     */
    public function lineRemoveCourse($id, $cid, LineManager $lineManager, TwigManager $twig)
    {
        $entity = $lineManager->find($id);

        $lineManager->removeActivity($cid);

        $form = $this->createForm(LineType::class, $entity, ['calendar_data' => $lineManager->getCalendar()]);

        $content = $this->renderView('Timetable/Line/line_collections.html.twig',
            [
                'collection' => $form->get('activities')->createView(),
            ]
        );

        return new JsonResponse(
            [
                'message' =>$lineManager->getMessageManager()->renderView($twig->getTwig()),
                'status' => $lineManager->getStatus(),
                'content' => $content,

            ],
            200
        );
    }

    /**
     * @param string|integer $id
     * @param LineManager $lineManager
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/line/{id}/test/", name="line_test")
     * @IsGranted("ROLE_PRINCIPAL")
     */
    public function test($id, LineManager $lineManager, TwigManager $twig)
    {
        $lineManager->find($id);

        $lineManager->getReport();

        return $this->render('Timetable/Line/report.html.twig',
            [
                'headerOff' => true,
                'fullPage' => true,
            ]
        );
    }

    /**
     * @Route("/timetable/{id}/assigned/days/", name="timetable_assign_days")
     * @IsGranted("ROLE_PRINCIPAL")
     * @param integer $id
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

        $flashBagManager->addMessages($timetableManager->getMessageManager()->getMessages());

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
            $timetableManager->getMessageManager()->add('warning', 'column.reset_time.missing', [], 'Timetable');
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
                $timetableManager->getMessageManager()->add('danger', 'column.reset_time.error', [], 'Timetable');
                return $this->forward(TimetableController::class . '::edit', ['id' => $id]);
            }
        }

        $timetableManager->getMessageManager()->add('success', 'column.reset_time.success', [], 'Timetable');
        return $this->forward(TimetableController::class . '::edit', ['id' => $id]);
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
        $report = $periodManager->generateFullPeriodReport();

        return $this->render('Timetable/Period/report.html.twig',
            [
                'report' => $report,
                'manager' => $periodManager,
            ]
        );
    }

    /**
     * editPeriodActivity
     *
     * @param Request $request
     * @param $activity
     * @param PeriodManager $periodManager
     * @param string $closeWindow
     * @Route("/period/activity/{activity}/edit/{closeWindow}", name="period_activity_edit")
     * @IsGranted("ROLE_PRINCIPAL")
     */
    public function editPeriodActivity(Request $request, $activity, PeriodManager $periodManager, $closeWindow = ''){}

    /**
     * addLineToPeriod
     *
     * @Route("/period/{id}/line/{line}/drop/", name="period_drop_line")
     * @IsGranted("ROLE_PRINCIPAL")
     * @param int $id
     * @param int $line
     * @param Request $request
     * @param PeriodManager $periodManager
     * @param TwigManager $twig
     * @return JsonResponse
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function addLineToPeriod(int $id, int $line, PeriodManager $periodManager, TwigManager $twig)
    {
        $periodManager->find($id);

        $periodManager->addLine($line);

        return new JsonResponse(
            [
                'status' => $periodManager->getMessageManager()->getHighestLevel(),
                'message' => $periodManager->getMessageManager()->renderView($twig->getTwig()),
            ],
            200);
    }

    /**
     * addActivityToPeriod
     *
     * @param int $id
     * @param int $activity
     * @param PeriodManager $periodManager
     * @param TwigManager $twig
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
     * removePeriodActivity
     *
     * @Route("/period/{id}/activity/{activity}/remove/", name="period_remove_activity")
     * @IsGranted("ROLE_PRINCIPAL")
     * @param $id
     * @param $activity
     * @param PeriodManager $periodManager
     * @param TwigManager $twig
     * @return JsonResponse
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
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
     * gradeControl
     *
     * @param $grade
     * @param $value
     * @Route("/grade/{id}/{value}/control/", name="grade_control")
     * @IsGranted("ROLE_PRINCIPAL")
     */
    public function gradeControl(int $id, bool $value, Request $request)
    {
        $session = $request->getSession();

        $gradeControl = $session->get('gradeControl');

        if (!is_array($gradeControl))
            $gradeControl = [];

        if (!isset($gradeControl[$id]))
            $gradeControl[$id] = boolval($value);

        $gradeControl[$id] = $gradeControl[$id] ? false : true;

        $session->set('gradeControl', $gradeControl);

        return new JsonResponse([], 200);
    }

    /**
     * builderLineActivity
     *
     * @param Request $request
     * @param LinePagination $linePagination
     * @IsGranted("ROLE_PRINCIPAL")
     * @Route("/timetable/{id}/line/builder/", name="timetable_builder_line_activity")
     */
    public function builderLineActivity(int $id, Request $request, LinePagination $linePagination, TimetableManager $timetableManager)
    {
        $linePagination->injectRequest($request);
        $timetableManager->find($id);
        $grades = $timetableManager->gradeControl();

        $linePagination->setDisplaySearch(false)
            ->setDisplaySort(false)
            ->setDisplayChoice(false)
            ->setSearch('')
            ->setLimit(1000)
            ->setCalendarGrades($grades)
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
     * builderActivity
     *
     * @IsGranted("ROLE_PRINCIPAL")
     * @Route("/timetable/{id}/activity/builder/", name="timetable_builder_activity")
     * @param Request $request
     * @param $id
     * @param TimetableManager $timetableManager
     * @param ActivityPagination $classPagination
     * @return JsonResponse
     */
    public function builderActivity(Request $request, $id,
                                       TimetableManager $timetableManager, ActivityPagination $classPagination) {
        $timetableManager->find($id);

        $grades = $timetableManager->gradeControl();

        $classPagination->injectRequest($request);

        $classPagination->setActivityType(
            [
                'class',
                'roll',
            ]
        );

        $classPagination->setLimit(1000)
            ->setSortByList(
                [
                    'activity.sort.name' =>            [
                        'a.name' => 'ASC',
                        'a.code' => 'ASC',
                    ],
                    'bySequence' => [
                        'cg.sequence' => 'ASC',
                        'a.name' => 'ASC',
                        'a.code' => 'ASC',
                    ],
                ]
            )
            ->setSortByName('bySequence')
            ->setDisplaySort(false)
            ->setDisplayChoice(false)
            ->setCalendarGrades($grades)
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
     * builderPeriod
     *
     * @param Request $request
     * @param $id
     * @param string $all
     * @param TimetableManager $timetableManager
     * @param PeriodPagination $periodPagination
     * @param PeriodManager $periodManager
     * @IsGranted("ROLE_PRINCIPAL")
     * @Route("/timetable/{id}/period/{all}/builder/", name="timetable_period_builder")
     */
    public function builderPeriod(Request $request, $id, $all = 'All',
                                     TimetableManager $timetableManager, PeriodPagination $periodPagination,
                                     PeriodManager $periodManager)
    {
        $timetable = $timetableManager->find($id);

        $grades = $timetableManager->gradeControl();

        $periodPagination->setTimetable($timetable);

        $periodPagination->injectRequest($request);

        $periodPagination->setLimit(1000)
            ->setDisplaySort(false)
            ->setDisplayChoice(false)
            ->setSearch('')
            ->setCalendarGrades($grades)
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
     * set TimetableGrade
     *
     * @param $id
     * @param Request $request
     * @return JsonResponse
     * @Route("/timetable/{id}/grade/", name="timetable_grade_set")
     * @IsGranted("ROLE_PRINCIPAL")
     */
    public function setTimetableGrade($id, Request $request)
    {
        $sess = $request->getSession();

        $sess->set('tt_identifier', 'grad' . $id);

        return new JsonResponse([], 200);

    }

    /**
     * Display Timetable
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/timetable/{id}/display/{closeWindow}", name="timetable_display")
     * @Security("is_granted('ROLE_PRINCIPAL')")
     */
    public function display(int $id, Request $request, VoterDetails $voterDetails, $closeWindow = '', TimetableDisplayManager $timetableDisplayManager)
    {
        $sess = $request->getSession();
        $timetableDisplayManager->find($id);

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
    public function setTimetableSpace($space){}

    /**
     * Set Timetable Staff
     *
     * @param $grade
     * @return JsonResponse
     * @Route("/timetable/staff/{staff}/set/", name="timetable_staff_set")
     * @IsGranted("ROLE_PRINCIPAL")
     */
    public function setTimetableStaff($staff, Request $request)
    {
        $sess = $request->getSession();

        $sess->set('tt_identifier', 'staf' . $staff);

        return new JsonResponse([], 200);
    }

    /**
     * searchPeriods
     *
     * @param $tt
     * @param $id
     * @Route("/timetable/{tt}/line/{id}/periods/search/", name="line_periods_search")
     * @IsGranted("ROLE_PRINCIPAL")
     */
    public function searchPeriods($tt, $id){}

    /**
     * Refresh Display TimeTable
     *
     * @param string $displayDate
     * @return JsonResponse
     * @Route("/timetable/{id}/display/{displayDate}/refresh/", name="timetable_refresh_display")
     * @IsGranted("ROLE_USER");
     */
    public function refreshDisplay($id, $displayDate, VoterDetails $voterDetails, TimetableDisplayManager $timetableDisplayManager, Request $request)
    {
        $sess = $request->getSession();

        $timetableDisplayManager->find($id);

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
     * @param UserInterface $user
     * @return null|string
     */
    public function getTimeTableIdentifier(UserInterface $user): ?string
    {
        // Determine if user is staff or student
        if (!$this->isDisplayTimetable()) {
            if ($this->getSession()->has('tt_identifier'))
                $this->getSession()->remove('tt_identifier');
            return null;
        }
        $identifier = '';

        if ($this->personManager->isStudent($user->getPerson())) {
            $identifier = 'stud' . $user->getPerson()->getStudent()->getId();
        }
        if ($this->personManager->isStaff($user->getPerson())) {
            $identifier = 'staf' . $user->getPerson()->getStaff()->getId();
        }
        $this->getSession()->set('tt_identifier', $identifier);

        return $identifier;
    }

    /**
     * Is Valid User
     *
     * @param UserInterface $user
     * @return bool
     */
    public function isValidUser(UserInterface $user): bool
    {
        return $user->hasPerson();
    }

    /**
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function generateTimeTable($id)
    {
        return $this->render('TimeTable/Display/generate.html.twig',
            [
                'id' => $id,
            ]
        );
    }
}