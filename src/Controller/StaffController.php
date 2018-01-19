<?php
namespace App\Controller;

use App\Core\Manager\MessageManager;
use App\Entity\Person;
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
	 * @IsGranted("ROLE_REGISTRAR")
	 * @return JsonResponse
	 */
	public function toggle($id, PersonManager $personManager, MessageManager $messageManager, \Twig_Environment $twig)
	{
		$person = $personManager->find($id);
		$messageManager->setDomain('Staff');

		if (!$person instanceof Person)
			return new JsonResponse(
				array(
					'message' => $messageManager->add('danger', 'staff.toggle.personMissing')->renderView($twig),
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
						'message' => $messageManager->add('success', 'staff.toggle.removeSuccess', ['%name%' => $person->formatName()]),
						'status'  => 'removed',
					),
					200
				);
			}
			else
			{
				return new JsonResponse(
					array(
						'message' => $messageManager->add('warning', 'staff.toggle.removeRestricted', ['%name%' => $person->formatName()]),
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
						'message' => $messageManager->add('success', 'staff.toggle.addSuccess', ['%name%' => $person->formatName()]),
						'status'  => 'added',
					),
					200
				);
			}
			else
			{
				return new JsonResponse(
					array(
						'message' => $messageManager->add('warning', 'staff.toggle.addRestricted', ['%name%' => $person->formatName()]),
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