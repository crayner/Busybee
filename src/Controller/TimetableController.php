<?php
namespace App\Controller;

use App\Core\Manager\FlashBagManager;
use App\Core\Manager\TwigManager;
use App\Pagination\LinePagination;
use App\Pagination\TimetablePagination;
use App\Timetable\Form\LineType;
use App\Timetable\Form\TimetableType;
use App\Timetable\Util\ColumnManager;
use App\Timetable\Util\LineManager;
use App\Timetable\Util\TimetableManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
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
    public function builder($id, $all = 'All')
    {}

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
            foreach($entity->getCourses()->getIterator() as $course)
                $em->persist($course->setLine($entity));
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
     * @Route("/line/{id}/course/{cid}/remove/", name="line_remove_course")
     * @IsGranted("ROLE_PRINCIPAL")
     */
    public function lineRemoveCourse($id, $cid, LineManager $lineManager, TwigManager $twig)
    {
        $entity = $lineManager->find($id);

        $lineManager->removeCourse($cid);

        $form = $this->createForm(LineType::class, $entity, ['calendar_data' => $lineManager->getCalendar()]);

        $content = $this->renderView('Timetable/Line/line_collections.html.twig',
            [
                'collection' => $form->get('courses')->createView(),
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
}