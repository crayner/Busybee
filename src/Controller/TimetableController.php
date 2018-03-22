<?php
namespace App\Controller;

use App\Pagination\TimetablePagination;
use App\Timetable\Form\TimetableType;
use App\Timetable\Util\TimetableManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TimetableController extends Controller
{
    /**
     * @Route("/timetable/list/", name="timetable_list")
     * @IsGranted("ROLE_PRINCIPAL")
     * @param TimetablePagination $timetablePagination
     * @param Request $request
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
}