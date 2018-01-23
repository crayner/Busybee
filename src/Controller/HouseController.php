<?php
namespace App\Controller;

use App\School\Form\HousesType;
use App\School\Util\HouseManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class HouseController extends Controller
{
	/**
	 * @param Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 * @Route("/institute/house/manage/", name="houses_edit")
	 * @IsGranted("ROLE_REGISTRAR")
	 */
	public function edit(Request $request, HouseManager $houseManager)
	{
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR');

		$form = $this->createForm(HousesType::class, $houseManager, ['deletePhoto' => $this->generateUrl('house_logo_delete', ['houseName' => '__imageDelete__'])]);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$houseManager->saveHouses($form);

			$form = $this->createForm(HousesType::class, $houseManager, ['deletePhoto' => $this->generateUrl('house_logo_delete', ['houseName' => '__imageDelete__'])]);
		}

		return $this->render('House/edit.html.twig',
			[
				'form'     => $form->createView(),
				'fullForm' => $form,
			]
		);
	}

	/**
	 * @param $houseName
	 *
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 * @Route("/institute/house/logo/{houseName}/delete/", name="house_logo_delete")
	 * @IsGranted("ROLE_REGISTRAR")
	 */
	public function deleteLogo($houseName, HouseManager $houseManager)
	{
		$houseManager->deleteLogo($houseName);

		return $this->redirectToRoute('houses_edit');
	}
}