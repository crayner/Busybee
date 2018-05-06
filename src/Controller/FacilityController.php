<?php
namespace App\Controller;

use App\Core\Manager\SettingManager;
use App\Core\Organism\Collection;
use App\Entity\Campus;
use App\Entity\Space;
use App\Pagination\SpacePagination;
use App\School\Form\CampusType;
use App\School\Form\FacilityCollectionType;
use App\School\Form\SpaceType;
use App\School\Organism\Facility;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class FacilityController extends Controller
{
	/**
	 * @Route("/campus/manage/{id}/", name="campus_manage")
	 * @IsGranted("ROLE_REGISTRAR")
	 */
	public function campusManage(Request $request, $id = 'Add')
	{
		$campus = new Campus();

		if (intval($id) > 0)
			$campus = $this->getDoctrine()->getManager()->getRepository(Campus::class)->find($id);

		$form = $this->createForm(CampusType::class, $campus);
		if (intval($id) > 0)
			$form->get('locationList')->setData($id);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$em = $this->getDoctrine()->getManager();
			$em->persist($campus);
			$em->flush();

			if ($id === 'Add')
			    return $this->redirectToRoute('campus_manage', ['id' => $campus->getId()]);

		}

		return $this->render('Facility/campus.html.twig', array(
				'form'     => $form->createView(),
				'fullForm' => $form,
			)
		);
	}
	/**
	 * @Route("/space/list/", name="space_list")
	 * @IsGranted("ROLE_REGISTRAR")
	 * @param Request         $request
	 * @param SpacePagination $spacePagination
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function spaceList(Request $request, SpacePagination $spacePagination)
	{
		$spacePagination->injectRequest($request);

		$spacePagination->getDataSet();

		return $this->render('Facility/spaces.html.twig',
			array(
				'pagination' => $spacePagination,
			)
		);
	}

    /**
     * @Route("/space/edit/{id}/", name="space_edit")
     * @IsGranted("ROLE_REGISTRAR")
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
	public function editSpace($id, Request $request)
	{
		$space = new Space();

		if (intval($id) > 0)
			$space = $this->getDoctrine()->getManager()->getRepository(Space::class)->find($id);

		$space->cancelURL = $this->get('router')->generate('space_list');

		$form = $this->createForm(SpaceType::class, $space);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$em = $this->get('doctrine')->getManager();

			$em->persist($space);
			$em->flush();

			if ($id === 'Add')
				return $this->redirectToRoute('space_edit', ['id' => $space->getId()]);
		}

		return $this->render('Facility/spaceEdit.html.twig', ['id' => $id, 'form' => $form->createView()]);
	}

	/**
	 * @Route("/space/duplicate/", name="space_duplicate")
	 * @IsGranted("ROLE_REGISTRAR")
	 */
	public function duplicateSpace(Request $request)
	{
		$this->denyAccessUnlessGranted('ROLE_ADMIN', null, null);

		$id = $request->get('space')['duplicateid'];

		if ($id === "Add")
			$space = new Space();
		else
			$space = $this->get('busybee_facility_institute.repository.space_repository')->find($id);

		$space->cancelURL = $this->generateUrl('campus_space_manage');

		$form = $this->createForm(SpaceType::class, $space);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$em = $this->get('doctrine')->getManager();

			$em->persist($space);
			$em->flush();

			$route = $this->generateUrl('space_edit', ['id' => 'Add']);
			$space->setId(null);
			$space->setName(null);
			$form = $this->createForm(SpaceType::class, $space, ['action' => $route]);
			$id   = 'Add';
		}


		return $this->render('BusybeeInstituteBundle:Campus:spaceEdit.html.twig',
			[
				'id'   => $id,
				'form' => $form->createView(),
			]
		);
	}

    /**
     * @param Request $request
     * @param SettingManager $settingManager
     * @throws \Exception
     * @Route("/facility/type/manage/", name="facility_type_manage")
     */
    public function facilityTypeManage(Request $request, SettingManager $settingManager)
    {
        $setting = $settingManager->get('space.type');

        $values = new ArrayCollection();

        foreach($setting as $q=>$w)
        {
            foreach($w as $r)
            {
                $ft = new Facility();
                $ft->setName($r);
                $ft->setTeachingSpace(false);
                if ($q === 'teaching_space')
                    $ft->setTeachingSpace(true);
                $values->add($ft);
            }
        }
        $data = new Collection();
        $data->setValues($values);
        $form = $this->createForm(FacilityCollectionType::class, $data);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $value = [];
            foreach($form->get('values')->getData()->getIterator() as $item)
            {
                if ($item->isTeachingSpace())
                    $value['teaching_space'][] = strtolower($item->getName());
                else
                    $value['non_teaching_space'][] = strtolower($item->getName());
            }
            sort($value['teaching_space']);
            sort($value['non_teaching_space']);
            $settingManager->set('space.type', $value);
            return $this->redirectToRoute('facility_type_manage');
        }

        return $this->render('Facility/facility_type.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}