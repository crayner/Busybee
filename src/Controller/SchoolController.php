<?php
namespace App\Controller;

use App\Pagination\RollGroupPagination;
use App\Repository\RollGroupRepository;
use App\School\Form\DaysTimesType;
use App\School\Form\RollGroupType;
use App\School\Util\DaysTimesManager;
use App\School\Util\RollGroupManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SchoolController extends Controller
{
	/**
	 * @param Request $request
	 * @Route("/school/days/times/", name="school_days_times")
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function daysAndTimes(Request $request, DaysTimesManager $dtm)
	{
		$form = $this->createForm(DaysTimesType::class, $dtm);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
			$dtm->saveDaysTimes($form);

		return $this->render('School/daysandtimes.html.twig',
			[
				'form'     => $form->createView(),
				'fullForm' => $form,
			]
		);
	}

    /**
     * @Route("/school/roll/list/", name="roll_list")
     * @IsGranted("ROLE_REGISTRAR")
     * @param Request $request
     * @param RollGroupPagination $rollGroupPagination
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function rollList(Request $request, RollGroupPagination $rollGroupPagination, RollGroupManager $rollGroupManager)
    {
        $rollGroupPagination->injectRequest($request);

        $rollGroupPagination->getDataSet();


        return $this->render('School/roll_list.html.twig',
            [
                'pagination' => $rollGroupPagination,
                'manager' => $rollGroupManager,
            ]
        );
    }

    /**
     * @Route("/school/roll/{id}/edit/", name="roll_edit")
     * @IsGranted("ROLE_REGISTRAR")
     * @param Request $request
     * @param int|string $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function rollEdit(Request $request, $id = 'Add', RollGroupRepository $rollGroupRepository, EntityManagerInterface $entityManager)
    {
        $rollGroup = $rollGroupRepository->find($id);

        $form = $this->createForm(RollGroupType::class, $rollGroup);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager->persist($rollGroup);
            $entityManager->flush();

            if ($id === 'Add')
                return $this->redirectToRoute('roll_edit', ['id' => $rollGroup->getId()]);
        }

        return $this->render('School/roll_edit.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}