<?php
namespace App\Controller;

use App\Entity\Timetable;
use App\Pagination\TimetablePagination;
use App\Timetable\Form\TimetableType;
use App\Timetable\Util\TimetableManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\MakerBundle\Validator;
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
     * @param \Twig_Environment $twig
     * @return JsonResponse
     */
    public function manageTTColumn($id = 'Add', $cid = 'ignore', TimetableManager $timetableManager, \Twig_Environment $twig)
    {
        $timetableManager->find($id);

        $timetableManager->manageTTColumn($cid);

        $form = $this->createForm(TimetableType::class, $timetableManager->getTimetable());

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
                'message' => $timetableManager->getMessageManager()->renderView($twig),
                'status' => $timetableManager->getStatus(),
            ],
            200
        );
    }
}