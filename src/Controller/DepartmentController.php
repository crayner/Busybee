<?php
namespace App\Controller;

use App\Core\Manager\FlashBagManager;
use App\Entity\Department;
use App\School\Form\DepartmentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DepartmentController extends Controller
{
	/**
	 * @param Request $request
	 * @Route("/institute/department/edit/{id}/", name="department_edit")
	 * @IsGranted("ROLE_PRINCIPAL")
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function edit(Request $request, $id, FlashBagManager $flashBagManager)
	{
		$entity = new Department();

		if (intval($id) > 0)
			$entity = $this->getDoctrine()->getRepository(Department::class)->find($id);

		$form = $this->createForm(DepartmentType::class, $entity, ['deletePhoto' => $this->generateUrl('department_logo_delete', ['id' => $id])]);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$em = $this->get('doctrine')->getManager();
			$em->persist($entity);
			$em->flush();
			$flashBagManager->setDomain('home');
			$flashBagManager->add('success', 'form.submit.success', [], 'home');

			if ($id == 'Add')
			{
				$count = 0;
				foreach ($entity->getStaff()->toArray() as $deptStaff)
				{
					$deptStaff->setDepartment($entity);
					$em->persist($deptStaff);
					$em->flush();
					$count++;
				}

				if ($count > 0)
					$flashBagManager->add('success', 'department.member.added.success', [], 'Department');
				$flashBagManager->addMessages();

				return $this->redirectToRoute('department_edit', ['id' => $entity->getId()]);
			}

			$form = $this->createForm(DepartmentType::class, $entity, ['deletePhoto' => $this->generateUrl('department_logo_delete', ['id' => $id])]);

		}

		$flashBagManager->addMessages();

		return $this->render('Department/edit.html.twig', array(
				'form'     => $form->createView(),
				'fullForm' => $form,
			)
		);
	}


	/**
	 * @param $id
	 * @Route("/institute/department/logo/delete/{id}/", name="department_logo_delete")
	 * @IsGranted("ROLE_PRINCIPAL")
	 * @return Response
	 */
	public function deleteLogo($id, Request $request)
	{
		$om     = $this->getDoctrine()->getManager();
		$entity = $om->getRepository(Department::class)->find($id);

		if ($entity instanceof Department)
		{
			$file = $entity->getLogo();
			if (file_exists($file))
				unlink($file);

			$entity->setLogo(null);
			$om->persist($entity);
			$om->flush();
		}

		return $this->forward('App\Controller\DepartmentController::edit', ['id' => $id, 'request' => $request]);
	}

	/**
	 * @param $id
	 * @Route("/institute/department/remove/member/{id}/", name="department_member_remove")
	 * @return JsonResponse
	 */
	public function removeMember($id)
	{
		$this->denyAccessUnlessGranted('ROLE_PRINCIPAL', null, null);

		$om = $this->getDoctrine()->getManager();

		$ds = $om->getRepository(DepartmentMember::class)->find($id);

		if ($ds instanceof DepartmentMember)
		{

			$data            = [];
			$data['status']  = 'success';
			$data['message'] = $this->get('translator')->trans('department.member.remove.success', [], 'BusybeeInstituteBundle');
			try
			{
				$om->remove($ds);
				$om->flush();
			}
			catch (\Exception $e)
			{
				$data['status']  = 'error';
				$data['message'] = $this->get('translator')->trans('department.member.remove.failure', [], 'BusybeeInstituteBundle');
			}

			return new JsonResponse($data, 200);
		}

		$data            = [];
		$data['message'] = $this->get('translator')->trans('department.member.remove.missing', [], 'BusybeeInstituteBundle');
		$data['status']  = 'warning';

		return new JsonResponse($data, 200);

	}
}