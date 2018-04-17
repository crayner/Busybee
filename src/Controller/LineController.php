<?php
namespace App\Controller;

use App\Pagination\LinePagination;
use App\Timetable\Form\LineType;
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
     * @param Request $request
     * @param LinePagination $linePagination
     * @return \Symfony\Component\HttpFoundation\Response
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

            $em->persist($entity);
            $em->flush();

            if ($id == 'Add') {
                $close = [];
                if (!empty($closeWindow))
                    $close = ['closeWindow' => '_closeWindow'];

                return $this->redirectToRoute('line_manage', array_merge(['id' => $entity->getId()], $close));
            }
        }

        return $this->render('Line/manage.html.twig',
            [
                'form' => $form->createView(),
                'fullForm' => $form,
            ]
        );
    }

    /**
     * @param string|integer $id
     * @param LineManager $lineManager
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/line/{id}/test/", name="line_test")
     * @IsGranted("ROLE_PRINCIPAL")
     */
    public function testAction($id = 'Add', LineManager $lineManager)
    {
        $this->denyAccessUnlessGranted('ROLE_PRINCIPAL', null, null);

        $lgm = $this->get('line.manager');

        $year = $this->get('busybee_core_security.doctrine.user_manager')->getSystemYear($this->getUser());

        $lgm->generateReport($id, $year);

        $data = $lgm->getReport();

        return $this->render('Line/report.html.twig',
            [
                'report' => $data['report'],
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
    public function deleteAction($id, LineManager $lineManager)
    {
        $lineManager->deleteLine($id);

        return $this->redirectToRoute('line_list');
    }
}