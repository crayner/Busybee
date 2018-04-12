<?php
namespace App\Controller;

use App\Collection\Form\CollectionTestType;
use App\Collection\Organism\Test;
use App\Collection\Organism\Value;
use App\Core\Manager\TwigManager;
use App\Timetable\Util\TimetableManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class TestController extends Controller
{
    /**
     * @Route("/test/page/", name="test_page")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function test(Request $request)
    {
        $timetable = new Test();

        $value = new Value();
        $value->setId(1);
        $value->setName('Craig');
        $timetable->addValue($value);
        $value = new Value();
        $value->setId(2);
        $value->setName('Malcolm');
        $timetable->addValue($value);
        $value = new Value();
        $value->setId(3);
        $value->setName('Coralie');
        $timetable->addValue($value);

        $form = $this->createForm(CollectionTestType::class, $timetable);

        $form->handleRequest($request);

        return $this->render('Collection/page.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/test/page/{id}/timetable/{cid}/column/", name="test_page_manage_column")
     * @param $id
     * @param string $cid
     * @param TimetableManager $timetableManager
     * @param TwigManager $twig
     * @return JsonResponse
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function testManageColumns($id, $cid = 'ignore', TimetableManager $timetableManager, TwigManager $twig)
    {
        $timetable = $timetableManager->find($id);

        $form = $this->createForm(TimetableTestType::class, $timetable, ['display_scripts' => true]);

        $content = $this->renderView('Collection/timetable_collection.html.twig',
            [
                'collection' => $form->get('columns')->createView(),
            ]
        );

        return new JsonResponse([
            'content' => $content,
            'message' => $timetableManager->getMessageManager()->renderView($twig->getTwig()),
            'status' => $timetableManager->getStatus(),
        ],200);
    }
}