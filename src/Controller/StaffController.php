<?php
namespace App\Controller;

use App\Pagination\StaffPagination;
use App\People\Util\PersonManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class StaffController extends Controller
{
	/**
	 * @param $id
	 * @Route("/person/staff/toggle/{id}/", name="person_toggle_staff")
	 * @IsGranted("ROLE_ADMIN")
	 * @return JsonResponse
	 */
	public function toggle($id)
	{
		$this->denyAccessUnlessGranted('ROLE_REGISTRAR', null, null);

		$personManager = $this->get('busybee_people_person.model.person_manager');

		$person = $personManager->find($id);

		if (!$person instanceof Person)
			return new JsonResponse(
				array(
					'message' => '<div class="alert alert-danger alert-dismissable show hide">' . $this->get('translator')->trans('staff.toggle.personMissing', array(), 'BusybeeStaffBundle') . '</div>',
					'status'  => 'failed'
				),
				200
			);
		$em = $this->get('doctrine')->getManager();

		if ($personManager->isStaff())
		{
			if ($personManager->canDeleteStaff())
			{
				$personManager->deleteStaff();

				return new JsonResponse(
					array(
						'message' => '<div class="alert alert-success alert-dismissable show hide">' . $this->get('translator')->trans('staff.toggle.removeSuccess', array('%name%' => $person->formatName()), 'BusybeeStaffBundle') . '</div>',
						'status'  => 'removed',
					),
					200
				);
			}
			else
			{
				return new JsonResponse(
					array(
						'message' => '<div class="alert alert-warning alert-dismissable show hide">' . $this->get('translator')->trans('staff.toggle.removeRestricted', array('%name%' => $person->formatName()), 'BusybeeStaffBundle') . '</div>',
						'status'  => 'failed',
					),
					200
				);
			}
		}
		else
		{
			if (!$personManager->isStaff() && $personManager->canBeStaff())
			{
				$personManager->createStaff();

				return new JsonResponse(
					array(
						'message' => '<div class="alert alert-success alert-dismissable show hide">' . $this->get('translator')->trans('staff.toggle.addSuccess', array('%name%' => $person->formatName()), 'BusybeeStaffBundle') . '</div>',
						'status'  => 'added',
					),
					200
				);
			}
			else
			{
				return new JsonResponse(
					array(
						'message' => '<div class="alert alert-warning alert-dismissable show hide">' . $this->get('translator')->trans('staff.toggle.addRestricted', array('%name%' => $person->formatName()), 'Person') . '</div>',
						'status'  => 'failed',
					),
					200
				);
			}
		}
	}

	/**
	 * @param Request         $request
	 * @param StaffPagination $staffPagination
	 * @param PersonManager   $personManager
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 * @Route("person/staff/list/", name="staff_manage")
	 * @IsGranted("ROLE_ADMIN")
	 */
	public function list(Request $request, StaffPagination $staffPagination, PersonManager $personManager)
	{
		$staffPagination->injectRequest($request);

		$staffPagination->getDataSet();

		return $this->render('Staff/index.html.twig',
			array(
				'pagination' => $staffPagination,
				'manager'    => $personManager,
			)
		);
	}
}