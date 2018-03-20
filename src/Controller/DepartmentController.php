<?php
namespace App\Controller;

use App\Core\Manager\FlashBagManager;
use App\Core\Manager\MessageManager;
use App\Entity\Department;
use App\Entity\DepartmentMember;
use App\School\Form\DepartmentType;
use App\School\Util\DepartmentManager;
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
     * @param $id
     * @param FlashBagManager $flashBagManager
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/institute/department/edit/{id}/", name="department_edit")
     * @IsGranted("ROLE_PRINCIPAL")
     */
	public function edit(Request $request, $id, FlashBagManager $flashBagManager, DepartmentManager $departmentManager)
	{
		$entity = new Department();

		if (intval($id) > 0)
			$entity = $this->getDoctrine()->getRepository(Department::class)->find($id);

		$form = $this->createForm(DepartmentType::class, $entity, ['deletePhoto' => $this->generateUrl('department_logo_delete', ['id' => $id])]);

		$form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
		{
dump([$form,$request]);
            $em = $this->get('doctrine')->getManager();
			$em->persist($entity);
			$em->flush();

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
					$flashBagManager->add('success', 'department.member.added.success', [], 'School');
				$flashBagManager->addMessages();

				return $this->redirectToRoute('department_edit', ['id' => $entity->getId()]);
			}

			$form = $this->createForm(DepartmentType::class, $entity, ['deletePhoto' => $this->generateUrl('department_logo_delete', ['id' => $id])]);

		}

		$flashBagManager->addMessages();

		return $this->render('Department/edit.html.twig', [
				'form'      => $form->createView(),
				'fullForm'  => $form,
                'tabManager'      => $departmentManager,
			]
		);
	}


    /**
     * @param $id
     * @param Request $request
     * @return Response
     * @Route("/institute/department/logo/delete/{id}/", name="department_logo_delete")
     * @IsGranted("ROLE_PRINCIPAL")
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
     * @param MessageManager $messageManager
     * @param \Twig_Environment $twig
     * @return JsonResponse
     * @Route("/institute/department/remove/member/{id}/", name="department_member_remove")
     * @IsGranted("ROLE_PRINCIPAL")
     */
	public function removeMember($id, MessageManager $messageManager, \Twig_Environment $twig)
	{
		$om = $this->getDoctrine()->getManager();

		$ds = $om->getRepository(DepartmentMember::class)->find($id);
		$data            = [];

		if ($ds instanceof DepartmentMember)
		{

			$data['status']  = 'success';
			$messageManager->add('success', 'department.member.remove.success', [], 'School');
			try
			{
				$om->remove($ds);
				$om->flush();
			}
			catch (\Exception $e)
			{
				$data['status']  = 'error';
				$messageManager->add('danger', 'department.member.remove.failure', ['%{message}' => $e->getMessage()], 'School');
			}

			$data['message'] = $messageManager->renderView($twig);
			return new JsonResponse($data, 200);
		}

		$data['message'] = 			$data['message'] = $messageManager->add('warning', 'department.member.remove.missing', [], 'School')->renderView($twig);
		$data['status']  = 'warning';

		return new JsonResponse($data, 200);
	}

    /**
     * @param string $cid
     * @param $id
     * @param DepartmentManager $departmentManager
     * @param \Twig_Environment $twig
     * @return JsonResponse
     * @Route("/department/courses/{id}/manage/{cid}", name="department_courses_manage")
     * @IsGranted("ROLE_PRINCIPAL")
     */
    public function manageCourseCollection($cid = 'ignore', $id, DepartmentManager $departmentManager, \Twig_Environment $twig)
    {
        $entity = $departmentManager ->findDepartment($id);

        $departmentManager->removeCourse($cid);

        $form = $this->createForm(DepartmentType::class, $entity, ['deletePhoto' => $this->generateUrl('department_logo_delete', ['id' => $id])]);

        return new JsonResponse(
            [
                'content' => $this->renderView("Department/department_collection.html.twig",
                    [
                        'collection' => $form->get('courses')->createView(),
                        'route' => 'department_courses_manage',
                        'contentTarget' => 'courseCollection',
                    ]
                ),
                'message' => $departmentManager->getMessageManager()->renderView($twig),
                'status' => $departmentManager->getStatus(),
            ],
            200
        );
    }

    /**
     * @param string $cid
     * @param $id
     * @param DepartmentManager $departmentManager
     * @param \Twig_Environment $twig
     * @return JsonResponse
     * @Route("/department/members/{id}/manage/{cid}", name="department_members_manage")
     * @IsGranted("ROLE_PRINCIPAL")
     */
    public function manageMemberCollection($cid = 'ignore', $id, DepartmentManager $departmentManager, \Twig_Environment $twig)
    {
        $departmentManager ->findDepartment($id);

        $departmentManager->removeMember($cid);

        $form = $this->createForm(DepartmentType::class, $departmentManager->refreshDepartment(), ['deletePhoto' => $this->generateUrl('department_logo_delete', ['id' => $id])]);

        $collection = $form->has('members') ? $form->get('members')->createView() : null;

        if (empty($collection))
            $departmentManager->getMessageManager()->add('warning', 'department.members.not_defined');

        $content = $this->renderView("Department/department_collection.html.twig",
            [
                'collection'    => $collection,
                'route'         => 'department_members_manage',
                'contentTarget' => 'memberCollection',
            ]
        );

        return new JsonResponse(
            [
                'content' => $content,
                'message' => $departmentManager->getMessageManager()->renderView($twig),
                'status'  => $departmentManager->getStatus(),
            ],
            200
        );
    }
}