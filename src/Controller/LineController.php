<?php
namespace App\Controller;

use App\Pagination\LinePagination;
use App\Timetable\Util\LineManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class LineController extends Controller
{
    /**
     * @Route("/line/list/", name="line_list")
     * @IsGranted("ROLE_PRINCIPAL")
     */
    public function lineList(Request $request, LinePagination $linePagination)
    {
        $linePagination->injectRequest($request);

        $linePagination->getDataSet();

        return $this->render('Line/list.html.twig',
            [
                'pagination' => $linePagination,
            ]
        );
    }

    /**
     * @param Request $request
     * @Route("/line/{id}/manage/{closeWindow}", name="line_manage")
     * @IsGranted("ROLE_PRINCIPAL")
     */
    public function lineManage(Request $request, $id, $closeWindow = null, LineManager $lineManager)
    {

    }
}