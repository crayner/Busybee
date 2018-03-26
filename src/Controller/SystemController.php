<?php
namespace App\Controller;

use App\Core\Form\ThirdPartyType;
use App\Core\Form\TranslateCollectionType;
use App\Core\Manager\MailerManager;
use App\Core\Manager\MessageManager;
use App\Core\Manager\TranslationManager;
use App\Core\Organism\ThirdParty;
use App\Core\Util\ThirdPartyManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SystemController extends Controller
{
    /**
     * @Route("/setting/string_replacement/", name="string_replacement")
     * @IsGranted("ROLE_SYSTEM_ADMIN")
     * @return Response
     */
    public function stringReplacement(Request $request, TranslationManager $translationManager): Response
    {
        $form = $this->createForm(TranslateCollectionType::class, $translationManager);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $write = $translationManager->getStrings();
            $was = $translationManager->getStrings(true);
            foreach($write->getIterator() as $entity)
            {
                $translationManager->getEntityManager()->persist($entity);
            }

            $remove = $was->filter(function($entry) use ($write) {
                return ! $write->contains($entry);
            });
            foreach($remove->getIterator() as $entity)
                $translationManager->getEntityManager()->remove($entity);
            $translationManager->getEntityManager()->flush();

            $translationManager->getStrings(true);
            $form = $this->createForm(TranslateCollectionType::class, $translationManager);
        }

        return $this->render('Setting/string_replacement.html.twig',
            [
                'form' => $form->createView(),
                'fullForm' => $form,
            ]
        );
    }

    /**
     * @Route("/setting/third_party/", name="third_party_settings")
     * @IsGranted("ROLE_SYSTEM_ADMIN")
     */
    public function thirdParty(ThirdPartyManager $thirdPartyManager, Request $request)
    {
        $form = $this->createForm(ThirdPartyType::class, $thirdPartyManager->getEntity());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $thirdPartyManager->saveGoogle();

            $thirdPartyManager->getMailerManager()->handleMailerRequest($form, $request, 'third_party');
        }

        return $this->render('Setting/thirdParty.html.twig',
            [
                'form' => $form->createView(),
                'tabManager' => $thirdPartyManager,
                'fullForm' => $form,
            ]
        );
    }

    /**
     * @Route("/setting/mailer/test/", name="mailer_test")
     * @IsGranted("ROLE_SYSTEM_ADMIN")
     */
    public function testMailDelivery(ThirdPartyManager $thirdPartyManager, \Twig_Environment $twig, MessageManager $messages, \Swift_Mailer $swiftMailer)
    {
        $thirdPartyManager->getMailerManager()->testDelivery($twig, $messages, $swiftMailer);

        return new JsonResponse(
            [
                'message' => $messages->renderView($twig),
            ],
            200
        );
    }
}