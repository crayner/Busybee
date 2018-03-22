<?php
namespace App\Controller;

use App\Pagination\TimetablePagination;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TimetableController extends Controller
{
    /**
     * @Route("/timetable/list/", name="timetable_list")
     * @IsGranted("ROLE_PRINCIPAL")
     */
    public function list(TimetablePagination $timetablePagination, Request $request)
    {
        $timetablePagination->injectRequest($request);

        $timetablePagination->getDataSet();

        return $this->render('Timetable/list.html.twig',
            [
                'pagination' => $timetablePagination,
            ]
        );
    }
    /**
     * @Route("/timetable/{id}/manage/", name="timetable_manage")
     * @IsGranted("ROLE_PRINCIPAL")
     */
    public function manage($id = 'Add', Request $request)
    {

        return $this->render('Timetable/manage.html.twig',
            [
            ]
        );
    }
}