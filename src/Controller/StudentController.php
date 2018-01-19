<?php
namespace App\Controller;

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
	public function toggle($id)
	{
		$this->denyAccessUnlessGranted('ROLE_ADMIN', null, null);

		$pm = $this->get('busybee_people_person.model.person_manager');

		$person = $pm->find($id);

		if (!$person instanceof Person)
			return new JsonResponse(
				array(
					'message' => '<div class="alert alert-danger alert-dismissable show hide">' . $this->get('translator')->trans('student.toggle.personMissing', array(), 'Student') . '</div>',
					'status'  => 'failed'
				),
				200
			);

		$em = $this->get('doctrine')->getManager();

		if (!$pm->isStudent())
		{
			if ($pm->canBeStudent())
			{
				$pm->createStudent($person);

				return new JsonResponse(
					array(
						'message' => '<div class="alert alert-success alert-dismissable show hide">' . $this->get('translator')->trans('student.toggle.addSuccess', array('%name%' => $person->formatName()), 'Student') . '</div>',
						'status'  => 'added',
					),
					200
				);
			}
			else
			{
				return new JsonResponse(
					array(
						'message' => '<div class="alert alert-warning alert-dismissable show hide">' . $this->get('translator')->trans('student.toggle.addRestricted', array('%name%' => $person->formatName()), 'Student') . '</div>',
						'status'  => 'failed',
					),
					200
				);
			}
		}
		elseif ($pm->isStudent())
		{
			if ($pm->canDeleteStudent(null, $this->getParameter('PersonTabs')))
			{
				$pm->deleteStudent(null, $this->getParameter('PersonTabs'));

				return new JsonResponse(
					array(
						'message' => '<div class="alert alert-success alert-dismissable show hide">' . $this->get('translator')->trans('student.toggle.removeSuccess', array('%name%' => $person->formatName()), 'Student') . '</div>',
						'status'  => 'removed',
					),
					200
				);

			}
			else
			{
				return new JsonResponse(
					array(
						'message' => '<div class="alert alert-warning alert-dismissable show hide">' . $this->get('translator')->trans('student.toggle.removeRestricted', array('%name%' => $person->formatName()), 'Student') . '</div>',
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

		$pm = $this->get('busybee_people_person.model.person_manager');

		$person = $pm->getPerson($id);

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

		$pm = $this->get('busybee_people_person.model.person_manager');

		$person = $pm->getPerson($id);

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