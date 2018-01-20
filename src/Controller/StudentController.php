<?php
namespace App\Controller;

use App\Core\Manager\MessageManager;
use App\Entity\Person;
use App\Pagination\StudentPagination;
use App\People\Util\PersonManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class StudentController extends Controller
{
	/**
	 * @param $id
	 * @Route("/person/student/toggle/{id}/", name="person_toggle_student")
	 * @IsGranted("ROLE_ADMIN")
	 * @return JsonResponse
	 */
	public function toggle($id, PersonManager $personManager, MessageManager $messageManager)
	{
		$person = $personManager->find($id);

		$messageManager->setDomain('Student');

		if (!$person instanceof Person)
			return new JsonResponse(
				array(
					'message' => $messageManager->add('danger', 'student.toggle.personMissing'),
					'status'  => 'failed'
				),
				200
			);

		$em = $this->get('doctrine')->getManager();

		if (!$personManager->isStudent())
		{
			if ($personManager->canBeStudent())
			{
				$personManager->createStudent($person);

				return new JsonResponse(
					array(
						'message' => $messageManager->add('success', 'student.toggle.addSuccess', ['%name%' => $person->formatName()]),
						'status'  => 'added',
					),
					200
				);
			}
			else
			{
				return new JsonResponse(
					array(
						'message' => $messageManager->add('warning', 'student.toggle.addRestricted', ['%name%' => $person->formatName()]),
						'status'  => 'failed',
					),
					200
				);
			}
		}
		elseif ($personManager->isStudent())
		{
			if ($personManager->canDeleteStudent(null, $this->getParameter('PersonTabs')))
			{
				$personManager->deleteStudent(null, $this->getParameter('PersonTabs'));

				return new JsonResponse(
					array(
						'message' => $messageManager->add('success', 'student.toggle.removeSuccess', ['%name%' => $person->formatName()]),
						'status'  => 'removed',
					),
					200
				);

			}
			else
			{
				return new JsonResponse(
					array(
						'message' => $messageManager->add('warning', 'student.toggle.removeRestricted', ['%name%' => $person->formatName()]),
						'status'  => 'failed',
					),
					200
				);
			}
		}
	}

	/**
	 * @param Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 * @Route("/person/student/list/", name="student_manage")
	 * @IsGranted("ROLE_ADMIN")
	 */
	public function listAction(Request $request, StudentPagination $studentPagination, PersonManager $personManager)
	{
		$studentPagination->injectRequest($request);

		$studentPagination->getDataSet();

		return $this->render('Student/index.html.twig',
			array(
				'pagination' =>  $studentPagination,
				'manager'    => $personManager,
			)
		);
	}
	/**
	 * @param $id
	 *
	 * @return RedirectResponse
	 * @Route("/person/student/remove/passport_scan/{id}/", name="student_passport_remove")
	 * @IsGranted("ROLE_ADMIN")
	 */
	public function removePassportScan(Request $request, $id)
	{
		$this->denyAccessUnlessGranted('ROLE_ADMIN');

		$personManager = $this->get('busybee_people_person.model.person_manager');

		$person = $personManager->getPerson($id);

		$em = $this->get('doctrine')->getManager();

		$photo = $person->getCitizenship1PassportScan();

		$person->setPhoto(null);

		if (file_exists($photo))
			unlink($photo);

		$em->persist($person);
		$em->flush();

		return $this->redirectToRoute('person_edit', ['id' => $id]);
	}

	/**
	 * @param $id
	 *
	 * @return RedirectResponse
	 * @Route("/person/student/remove/id_scan/{id}/", name="student_id_remove")
	 * @IsGranted("ROLE_ADMIN")
	 */
	public function removeIDScanAction(Request $request, $id)
	{
		$this->denyAccessUnlessGranted('ROLE_ADMIN');

		$personManager = $this->get('busybee_people_person.model.person_manager');

		$person = $personManager->getPerson($id);

		$em = $this->get('doctrine')->getManager();

		$photo = $person->getNationalIDScan();

		$person->setPhoto(null);

		if (file_exists($photo))
			unlink($photo);

		$em->persist($person);
		$em->flush();

		return $this->redirectToRoute('person_edit', ['id' => $id]);
	}
}