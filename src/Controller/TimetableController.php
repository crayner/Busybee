<?php
namespace App\Controller;

use App\Pagination\TimetablePagination;
use App\Timetable\Form\ColumnType;
use App\Timetable\Form\TimetableType;
use App\Timetable\Util\TimetableManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TimetableController extends Controller
{
    /**
     * @Route("timetable/manage/", name="timetable_manage")
     * @IsGranted("ROLE_PRINCIPAL")
     * @param Request $request
     * @param TimetablePagination $up
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function manage(Request $request, TimetablePagination $up) // was listAction
    {
        $up->injectRequest($request);

        $up->getDataSet();

        return $this->render('Timetable/manage.html.twig',
            [
                'pagination' => $up,
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
    public function editAction(Request $request, $id, TimetableManager $timetableManager)
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
     * @return  \Symfony\Component\HttpFoundation\Response
     * @IsGranted("ROLE_PRINCIPAL")
     * @Route("/timetable/{id}/days/edit/", name="timetable_days_edit")
     */
    public function editTimeTableDays(Request $request, $id, TimetableManager $timetableManager)
    {
        $entity = $timetableManager->find($id);

        $form = $this->createForm(ColumnType::class, $entity);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $om = $this->get('doctrine')->getManager();
            $om->persist($entity);
            $om->flush();
        }

        return $this->render('Timetable/Column/edit.html.twig',
            [
                'form' => $form->createView(),
                'fullForm' => $form,
                'timetable' => $entity,
                'tabManager' => $timetableManager,
            ]
        );
    }

    /**
     * @param   Request $request
     * @return  \Symfony\Component\HttpFoundation\Response
     * @IsGranted("ROLE_PRINCIPAL")
     * @Route("/timetable/{id}/builder/{all}/", name="timetable_builder")
     */
    public function builderAction(Request $request, $id, $all = 'All')
    {
        $tm = $this->get('timetable.manager')->setTimeTable($this->get('timetable.repository')->find($id));

//	    if ($tm->getTimeTable()->getLocked() && ! $tm->getTimeTable()->getGenerated())
//		    return $this->generateTimeTable($id);

        $up = $this->get('period.pagination');
        $lp = $this->get('line.pagination');
        $ap = $this->get('activity.pagination');

        $ap->injectRequest($request);
        $up->injectRequest($request);
        $lp->injectRequest($request);

        $up->setLimit(1000)
            ->setDisplaySort(false)
            ->setDisplayChoice(false)
            ->setDisplayResult(false);

        $gradeControl = $this->get('session')->get('gradeControl');
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

        $ap->setLimit(1000)
            ->setDisplaySort(false)
            ->setDisplayChoice(false)
            ->setSearch('')
            ->addInjectedSearch($search)
            ->setDisplayResult(false);

        $lp->setDisplaySearch(false)
            ->setDisplaySort(false)
            ->setDisplayChoice(false)
            ->setSearch('')
            ->setLimit(1000)
            ->addInjectedSearch($search)
            ->setDisplayResult(false);

        $up->getDataSet();
        $lp->getDataSet();
        $ap->getDataSet();

        $report = $tm->getReport($up);

        return $this->render('Timetable:TimeTable:builder.html.twig',
            [
                'pagination' => $up,
                'line_pagination' => $lp,
                'activity_pagination' => $ap,
                'pm' => $this->get('period.manager'),
                'all' => $all,
                'report' => $report,
                'grades' => $this->get('busybee_core_calendar.model.grade_manager')->getYearGrades(),
            ]
        );
    }

    /**
     * @IsGranted("ROLE_PRINCIPAL")
     * @Route("/timetable/column/{id}/remove/", name="column_remove")
     */
    public function removeColumn($id)
    {
        $column = $this->get('column.repository')->find($id);


        if (empty($column)) {
            $this->get('session')->getFlashBag()->add('success', 'column.remove.missing');
            return new RedirectResponse($this->generateUrl('timetable_edit', ['id' => $column->getTimeTable()->getId()]));
        }


        if (!$column->canDelete($id)) {
            $this->get('session')->getFlashBag()->add('warning', 'column.remove.locked');
            return new RedirectResponse($this->generateUrl('timetable_edit', ['id' => $column->getTimeTable()->getId()]));
        }

        try {
            $om = $this->get('doctrine')->getManager();
            $om->remove($column);
            $om->flush();
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add('danger', 'column.remove.error');
            return new RedirectResponse($this->generateUrl('timetable_edit', ['id' => $column->getTimeTable()->getId()]));
        }

        $this->get('session')->getFlashBag()->add('success', 'column.remove.success');

        return new RedirectResponse($this->generateUrl('timetable_edit', ['id' => $column->getTimeTable()->getId()]));
    }

    /**
     * @Route("/timetable/column/{id}/reset/times/", name="column_resettimes")
     */
    public function resetColumnTimes($id)
    {
        $this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, null);


        $tt = $this->get('timetable.repository')->find($id);
        if (empty($tt)) {
            $this->get('session')->getFlashBag()->add('warning', 'column.resettime.missing');
            return new RedirectResponse($this->generateUrl('timetable_edit', ['id' => $id]));
        }

        $sm     = $this->get('busybee_core_system.setting.setting_manager');
        $begin  = new \DateTime('1970-01-01 ' . $sm->get('SchoolDay.Begin'));
        $finish = new \DateTime('1970-01-01 ' . $sm->get('SchoolDay.Finish'));
        $om     = $this->get('doctrine')->getManager();

        if ($tt->getColumns()->count() > 0) {
            try {
                foreach ($tt->getColumns() as $column) {
                    $column->setStart($begin);
                    $column->setEnd($finish);
                    $om->persist($column);
                }
                $om->flush();
            } catch (\Exception $e) {
                $this->get('session')->getFlashBag()->add('danger', 'column.resettime.error');
                return new RedirectResponse($this->generateUrl('timetable_edit', ['id' => $column->getTimeTable()->getId()]));
            }
        }

        $this->get('session')->getFlashBag()->add('success', 'column.resettime.success');
        return new RedirectResponse($this->generateUrl('timetable_edit', ['id' => $column->getTimeTable()->getId()]));
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
                    'message' => '<div class="alert alert-danger fadeAlert">' . $this->get('translator')->trans('period.remove.missing', [], 'BusybeeTimeTableBundle') . '</div>',
                ),
                200
            );
        }

        $message = '<div class="alert alert-warning fadeAlert">' . $this->get('translator')->trans('period.remove.locked', [], 'BusybeeTimeTableBundle') . '</div>';

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


        $message = '<div class="alert alert-success fadeAlert">' . $this->get('translator')->trans('period.remove.success', [], 'BusybeeTimeTableBundle') . '</div>';

        return new JsonResponse(
            array(
                'page' => $page,
                'status' => $status,
                'message' => $message,
            ),
            200
        );
    }
}