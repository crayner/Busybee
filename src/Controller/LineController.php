<?php
namespace App\Controller;

use App\Core\Manager\TranslationManager;
use App\Core\Manager\TwigManager;
use App\Pagination\LinePagination;
use App\Timetable\Form\LineType;
use App\Timetable\Util\LineManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    public function test($id, LineManager $lineManager, TwigManager $twig)
    {
        $lineManager->find($id);

        $lineManager->generateReport($id);

        $data = $lineManager->getReport();

        $lineManager->getMessageManager()->setUseRaw(true);

        return $this->render('Line/report.html.twig',
            [
                'headerOff' => true,
                'fullPage' => true,

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
    public function delete($id, LineManager $lineManager)
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
    public function removeCourse($id, $cid, LineManager $lineManager, TwigManager $twig)
    {
        $entity = $lineManager->find($id);

        $lineManager->removeCourse($cid);

        $form = $this->createForm(LineType::class, $entity, ['calendar_data' => $lineManager->getCalendar()]);

        $content = $this->renderView('Line/line_collections.html.twig',
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
}