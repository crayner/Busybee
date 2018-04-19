<?php
namespace App\Controller;

use App\Core\Manager\FlashBagManager;
use App\Core\Manager\MessageManager;
use App\Core\Manager\TwigManager;
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
     * @Route("/department/{id}/edit/", name="department_edit")
     * @IsGranted("ROLE_PRINCIPAL")
     */
	public function edit(Request $request, $id, FlashBagManager $flashBagManager, DepartmentManager $departmentManager)
	{
		$entity = $departmentManager->findDepartment($id);

		$form = $this->createForm(DepartmentType::class, $entity, ['deletePhoto' => $this->generateUrl('department_logo_delete', ['id' => $id])]);

		$form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
		{
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
     * @Route("/department/logo/delete/{id}/", name="department_logo_delete")
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
     * @param string $cid
     * @param $id
     * @param DepartmentManager $departmentManager
     * @param \Twig_Environment $twig
     * @return JsonResponse
     * @Route("/department/{id}/courses/{cid}/manage/", name="department_courses_manage")
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
                        'contentTarget' => 'department_courses_target',
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
     * @Route("/department/{id}/members/{cid}/manage/", name="department_members_manage")
     * @IsGranted("ROLE_PRINCIPAL")
     */
    public function manageMemberCollection($cid = 'ignore', $id, DepartmentManager $departmentManager, TwigManager $twig)
    {
        $departmentManager->removeMember($id, $cid);

        $form = $this->createForm(DepartmentType::class, $departmentManager->refreshDepartment(), ['deletePhoto' => $this->generateUrl('department_logo_delete', ['id' => $id])]);

        $collection = $form->has('members') ? $form->get('members')->createView() : null;

        if (empty($collection))
            $departmentManager->getMessageManager()->add('warning', 'department.members.not_defined');

        $content = $this->renderView("Department/department_collection.html.twig",
            [
                'collection'    => $collection,
                'route'         => 'department_members_manage',
                'contentTarget' => 'department_members_target',
            ]
        );

        return new JsonResponse(
            [
                'content' => $content,
                'message' => $departmentManager->getMessageManager()->renderView($twig->getTwig()),
                'status'  => $departmentManager->getStatus(),
            ],
            200
        );
    }
}