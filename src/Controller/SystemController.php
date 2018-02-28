<?php
namespace App\Controller;

use App\Core\Form\TranslateCollectionType;
use App\Core\Manager\TranslationManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SystemController extends Controller
{
    /**
     * @Route("/setting/string_replacement/", name="string_replacement")
     * @IsGranted("ROLE_SYSTEM_ADMIN")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function stringReplacement(Request $request, TranslationManager $translationManager)
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
}