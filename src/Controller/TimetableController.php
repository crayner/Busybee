<?php
namespace App\Controller;

use App\Core\Manager\TwigManager;
use App\Pagination\TimetablePagination;
use App\Timetable\Form\ColumnType;
use App\Timetable\Form\TimetableType;
use App\Timetable\Util\ColumnManager;
use App\Timetable\Util\TimetableManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class TimetableController extends Controller
{
    /**
     * @Route("/timetable/list/", name="timetable_list")
     * @IsGranted("ROLE_PRINCIPAL")
     * @param TimetablePagination $timetablePagination
     * @param Request $request
     * @param TimetableManager $timetableManager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function list(TimetablePagination $timetablePagination, Request $request, TimetableManager $timetableManager)
    {
        $timetablePagination->injectRequest($request);

        $timetablePagination->getDataSet();

        return $this->render('Timetable/list.html.twig',
            [
                'pagination' => $timetablePagination,
                'manager' => $timetableManager,
            ]
        );
    }

    /**
     * @Route("/timetable/{id}/manage/", name="timetable_manage")
     * @IsGranted("ROLE_PRINCIPAL")
     * @param string $id
     * @param Request $request
     * @param TimetableManager $timetableManager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function manage($id = 'Add', Request $request, TimetableManager $timetableManager)
    {
        $timetableManager->find($id);

        $form = $this->createForm(TimetableType::class, $timetableManager->getTimetable());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $timetableManager->getEntityManager();
            $em->persist($timetableManager->getTimetable());
            $em->flush();

            if ($id === 'Add')
                $this->redirectToRoute('timetable_manage', ['id' => $timetableManager->getTimetable()->getId()]);
        }
        return $this->render('Timetable/manage.html.twig',
            [
                'manager' => $timetableManager,
                'fullForm' => $form,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/timetable/{id}/tt_day/{cid}/manage/", name="timetable_day_manage")
     * @IsGranted("ROLE_PRINCIPAL")
     * @param string $id
     * @param string $cid
     * @param TimetableManager $timetableManager
     * @param \Twig_Environment $twig
     * @return JsonResponse
     */
    public function manageTTDay($id = 'Add', $cid = 'ignore', TimetableManager $timetableManager, \Twig_Environment $twig)
    {
        $timetableManager->find($id);

        $timetableManager->manageTTDay($cid);

        $form = $this->createForm(TimetableType::class, $timetableManager->getTimetable());

        $content = $this->renderView("Timetable/timetable_collection.html.twig",
            [
                'collection' => $form->get('days')->createView(),
                'route' => 'timetable_day_manage',
                'contentTarget' => 'dayCollection',
            ]
        );

        return new JsonResponse(
            [
                'content' => $content,
                'message' => $timetableManager->getMessageManager()->renderView($twig),
                'status' => $timetableManager->getStatus(),
            ],
            200
        );
    }

    /**
     * @Route("/timetable/{id}/tt_column/{cid}/manage/", name="timetable_column_manage")
     * @IsGranted("ROLE_PRINCIPAL")
     * @param string $id
     * @param string $cid
     * @param TimetableManager $timetableManager
     * @param TwigManager $twig
     * @return JsonResponse
     */
    public function manageTTColumn($id = 'Add', $cid = 'ignore', TimetableManager $timetableManager, TwigManager $twig)
    {
        $timetableManager->find($id);

        $timetableManager->manageTTColumn($cid);

        $form = $this->createForm(TimetableType::class, $timetableManager->getTimetable());

        $twig->setManager($timetableManager);

        $content = $this->renderView("Timetable/timetable_collection.html.twig",
            [
                'collection' => $form->get('columns')->createView(),
                'route' => 'timetable_day_manage',
                'contentTarget' => 'columnCollection',
            ]
        );

        return new JsonResponse(
            [
                'content' => $content,
                'message' => $timetableManager->getMessageManager()->renderView($twig->getTwig()),
                'status' => $timetableManager->getStatus(),
            ],
            200
        );
    }

    /**
     * @Route("/timetable/column/{id}/period/manage/", name="timetable_column_period_manage")
     * @IsGranted("ROLE_PRINCIPAL")
     */
    public function manageColumnPeriods(int $id, Request $request, ColumnManager $columnManager, TwigManager $twig)
    {
        $twig->setManager($columnManager);

        $columnManager->find($id);

        $form = $this->createForm(ColumnType::class, $columnManager->getEntity());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $columnManager->getEntityManager();
            $em->persist($columnManager->getEntity());
            $em->flush();
        }

        return $this->render('Timetable/Period/manage.html.twig',
            [
                'manager' => $columnManager,
                'fullForm' => $form,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/timetable/column/{id}/period/{cid}/remove/", name="timetable_column_period_remove")
     * @IsGranted("ROLE_PRINCIPAL")
     */
    public function removeColumnPeriods(int $id, $cid= 'ignore', Request $request, ColumnManager $columnManager, TwigManager $twig)
    {
        $columnManager->find($id);

        $columnManager->removePeriod($cid);

        $form = $this->createForm(ColumnType::class, $columnManager->getEntity());

        $form->handleRequest($request);

        $twig->setManager($columnManager);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $columnManager->getEntityManager();
            $em->persist($columnManager->getEntity());
            $em->flush();
        }

        $content = $this->renderView('Timetable/Period/collection.html.twig',
            [
                'collection' => $form->get('periods')->createView(),
                'route' => 'timetable_column_period_remove',
                'contentTarget' => 'periodCollection',
            ]
        );

        return new JsonResponse(
            [
                'content' => $content,
                'message' => $columnManager->getMessageManager()->renderView($twig->getTwig()),
                'status' => $columnManager->getStatus(),
            ],
            200
        );

    }
}