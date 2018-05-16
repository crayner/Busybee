<?php
namespace App\Controller;

use App\Core\Manager\FlashBagManager;
use App\Pagination\ClassPagination;
use App\Pagination\CoursePagination;
use App\Pagination\ExternalActivityPagination;
use App\Pagination\RollPagination;
use App\School\Form\RollType;
use App\School\Form\CourseType;
use App\School\Form\DaysTimesType;
use App\School\Form\ExternalActivityType;
use App\School\Form\FaceToFaceType;
use App\School\Util\ActivityManager;
use App\School\Util\CourseManager;
use App\School\Util\DaysTimesManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SchoolController extends Controller
{
    /**
     * @param Request $request
     * @param DaysTimesManager $dtm
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/school/days/times/", name="school_days_times")
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
     *
     * @Route("/school/course/list/", name="course_list")
     * @IsGranted("ROLE_REGISTRAR")
     * @param CoursePagination $coursePagination
     * @param Request $request
     * @param CourseManager $courseManager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function courseList(CoursePagination $coursePagination, Request $request, CourseManager $courseManager)
    {
        $coursePagination->injectRequest($request);

        $coursePagination->getDataSet();

        return $this->render('School/course_list.html.twig',
            [
                'pagination' => $coursePagination,
                'manager' => $courseManager,
            ]
        );
    }

    /**
     *
     * @Route("/school/course/{id}/edit/", name="course_edit")
     * @IsGranted("ROLE_PRINCIPAL")
     * @param $id
     * @param Request $request
     * @param ClassPagination $classPagination
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function courseEdit($id, Request $request, ClassPagination $classPagination, CourseManager $courseManager)
    {
        $course = $courseManager->find($id, true);

        $form = $this->createForm(CourseType::class, $course);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($course);
            $em->flush();

            if ($id === 'Add')
                return $this->redirectToRoute('course_edit', ['id' => $course->getId()]);
        }

        $classPagination->setCourse($course);

        $classPagination->injectRequest($request);

        $classPagination->getDataSet();


        return $this->render('School/course_edit.html.twig',
            [
                'form' => $form->createView(),
                'fullForm' => $form,
                'pagination' => $classPagination,
                'manager' => $courseManager,
            ]
        );
    }

    /**
     * courseActivityRemove
     *
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/course/{id}/activity/{aid}/remove/", name="course_activity_remove")
     * @IsGranted("ROLE_PRINCIPAL")
     */
    public function courseActivityRemove(int $id, int $aid, CourseManager $courseManager, FlashBagManager $flashBagManager)
    {
        $courseManager->find($id);

        $courseManager->removeActivity($aid);

        $flashBagManager->addMessages($courseManager->getMessageManager());

        return $this->redirectToRoute('course_edit', ['id' => $id, '_fragment' => 'classlist']);
    }

    /**
     * @Route("/school/roll/list/", name="roll_list")
     * @IsGranted("ROLE_REGISTRAR")
     * @param Request $request
     * @param RollPagination $activityPagination
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function rollList(Request $request, RollPagination $activityPagination)
    {
        $activityPagination->injectRequest($request);

        $activityPagination->getDataSet();

        return $this->render('School/roll_list.html.twig',
            [
                'pagination' => $activityPagination,
            ]
        );
    }

    /**
     * @Route("/school/roll/{id}/edit/{closeWindow}", name="roll_edit")
     * @IsGranted("ROLE_REGISTRAR")
     * @param Request $request
     * @param string $id
     * @param ActivityManager $activityManager
     * @param null $closeWindow
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function rollEdit(Request $request, $id = 'Add', ActivityManager $activityManager, string $closeWindow = null)
    {
        $activity = $activityManager->setActivityType('roll')->findActivity($id);

        $form = $this->createForm(RollType::class, $activity);
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid())
        {
            $activityManager->getEntityManager()->persist($activity);
            $activityManager->getEntityManager()->flush();

            if ($id === 'Add')
                return $this->redirectToRoute('roll_edit', ['id' => $activity->getId(),  'closeWindow' => $closeWindow]);
        }
    
        return $this->render('School/activity_edit.html.twig',
            [
                'form' => $form->createView(),
                'fullForm' => $form,
                'tabManager' => $activityManager,
            ]
        );
    }

    /**
     * activityReturn
     *
     * @param $id
     * @param ActivityManager $activityManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/activity/{id}/return/{hint}/{closeWindow}", name="activity_return")
     */
    public function activityReturn($id, string $hint = 'activity', string $closeWindow = null, ActivityManager $activityManager)
    {
        if ($hint === 'activity')
            $activityManager->setActivityType('activity')->findActivity($id);
        else
            $activityManager->setActivityType($hint);

        switch ($activityManager->getActivityType())
        {
            case 'class':
                return $this->redirectToRoute('course_edit', ['id' => $activityManager->getActivity()->getCourse()->getId(), '_fragment' => 'classlist', 'closeWindow' => $closeWindow]);
                break;
            case 'roll':
                return $this->redirectToRoute('roll_list');
                break;
            case 'external':
                return $this->redirectToRoute('external_activity_list');
            default:
                throw new \TypeError(sprintf('000 The Activity type could not be determined. %s', $activityManager->getActivityType()));
        }
    }

    /**
     * activityReturn
     *
     * @param $id
     * @param ActivityManager $activityManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/activity/{id}/edit/{hint}/{closeWindow}", name="activity_edit")
     */
    public function activityEdit($id, string $hint = 'activity', string $closeWindow = null, ActivityManager $activityManager)
    {
        if ($hint === 'activity')
            $activityManager->setActivityType('activity')->findActivity($id);
        else
            $activityManager->setActivityType($hint);

        switch ($activityManager->getActivityType())
        {
            case 'class':
                return $this->redirectToRoute('face_to_face_edit', ['id' => $activityManager->getActivity()->getId(), 'closeWindow' => $closeWindow]);
                break;
            case 'roll':
                return $this->redirectToRoute('roll_edit', ['id' => $activityManager->getActivity()->getId(), 'closeWindow' => $closeWindow]);
                break;
            case 'external':
                return $this->redirectToRoute('external_activity_edit', ['id' => $activityManager->getActivity()->getId(), 'closeWindow' => $closeWindow]);
            default:
                throw new \TypeError(sprintf('000 The Activity type could not be determined. %s', $activityManager->getActivityType()));
        }
    }

    /**
     * @Route("/school/face_to_face/{id}/course/{course_id}/edit/{closeWindow}", name="face_to_face_edit")
     * @IsGranted("ROLE_PRINCIPAL")
     * @param Request $request
     * @param string $id
     * @param string $course_id
     * @param null $closeWindow
     * @param ActivityManager $activityManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function faceToFaceEdit(Request $request, $id = 'Add', $course_id = 'ignore', string $closeWindow = null, ActivityManager $activityManager)
    {
        $activityManager->setActivityType('class');

        $face = $activityManager->findActivity($id);
        if ($id === 'Add')
            $face->setCourse($activityManager->findCourse($course_id));

        $course_id = $face->getCourse()->getId();

        $form = $this->createForm(FaceToFaceType::class, $face);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $face->setCalendarGrades(null);
            foreach($face->getCourse()->getCalendarGrades()->getIterator() as $cg)
                $face->addCalendarGrade($cg);

            $activityManager->getEntityManager()->persist($face);
            $activityManager->getEntityManager()->flush();

            if ($id === 'Add')
                return $this->redirectToRoute('face_to_face_edit', ['id' => $face->getId(), 'closeWindow' => $closeWindow]);

            $face->getStudents(true);
            $form = $this->createForm(FaceToFaceType::class, $face);
        }

        return $this->render('School/activity_edit.html.twig',
            [
                'form' => $form->createView(),
                'tabManager' => $activityManager,
                'fullForm' => $form,
            ]
        );
    }

    /**
     * @Route("/school/external/activity/list/", name="external_activity_list")
     * @IsGranted("ROLE_PRINCIPAL")
     * @param Request $request
     * @param ExternalActivityPagination $activityPagination
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function externalActivityList(Request $request, ExternalActivityPagination $activityPagination)
    {
        $activityPagination->injectRequest($request);

        $activityPagination->getDataSet();

        return $this->render('School/external_activity_list.html.twig',
            [
                'pagination' => $activityPagination,
            ]
        );
    }

    /**
     * @Route("/school/activity/{id}/external/edit/{refresh}/{closeWindow}", name="external_activity_edit")
     * @IsGranted("ROLE_PRINCIPAL")
     * @param Request $request
     * @param $id
     * @param ActivityManager $activityManager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function externalActivityEdit(Request $request, $id = 'Add', $refresh = false, string $closeWindow = null, ActivityManager $activityManager)
    {
        $activity = $activityManager->setActivityType('external')->findActivity($id);

        $form = $this->createForm(ExternalActivityType::class, $activity);

        if (!$refresh)
        {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $activityManager->getEntityManager()->persist($activity);
                $activityManager->getEntityManager()->flush();

                if ($id === 'Add')
                    return $this->forward(SchoolController::class . '::externalActivityEdit', ['id' => $activity->getId(), 'closeWindow' => $closeWindow]);
            }
        }
        return $this->render('School/activity_edit.html.twig',
            [
                'form' => $form->createView(),
                'tabManager' => $activityManager,
                'fullForm' => $form,
            ]
        );
    }

    /**
     * @param string $id
     * @param string $cid
     * @param ActivityManager $activityManager
     * @param \Twig_Environment $twig
     * @return JsonResponse
     * @Route("/school/activity/external/{id}/tutor/{cid}/manage/", name="external_activity_tutor_manage")
     * @IsGranted("ROLE_PRINCIPAL")
     */
    public function externalActivityTutorManage($id = 'Add', $cid = 'ignore', ActivityManager $activityManager, \Twig_Environment $twig)
    {
        //if cid != ignore, then remove cid from collection
        $activity = $activityManager->setActivityType('external')->findActivity($id);

        if (intval($cid) > 0)
            $activityManager->removeTutor($cid);

        $form = $this->createForm(ExternalActivityType::class, $activity);

        return new JsonResponse(
            [
                'content' => $this->renderView("School/external_activity_collection.html.twig",
                    [
                        'collection' => $form->get('tutors')->createView(),
                        'route' => 'external_activity_tutor_manage',
                        'contentTarget' => 'activity_tutors_target',
                    ]
                ),
                'message' => $activityManager->getMessageManager()->renderView($twig),
                'status' => $activityManager->getStatus(),
            ],
            200
        );
    }

    /**
     * @param string $id
     * @param string $cid
     * @param ActivityManager $activityManager
     * @param \Twig_Environment $twig
     * @return JsonResponse
     * @Route("/school/activity/external/{id}/student/{cid}/manage/", name="external_activity_student_manage")
     * @IsGranted("ROLE_PRINCIPAL")
     */
    public function externalActivityStudentManage($id = 'Add', $cid = 'ignore', ActivityManager $activityManager, \Twig_Environment $twig)
    {
        //if cid != ignore, then remove cid from collection
        $activity = $activityManager->setActivityType('external')->findActivity($id);

        if (intval($cid) > 0)
            $activityManager->removeStudent($cid);

        $form = $this->createForm(ExternalActivityType::class, $activity);

        return new JsonResponse(
            [
                'content' => $this->renderView("School/external_activity_collection.html.twig",
                    [
                        'collection' => $form->get('students')->createView(),
                        'route' => 'external_activity_student_manage',
                        'contentTarget' => 'activity_students_target',
                    ]
                ),
                'message' => $activityManager->getMessageManager()->renderView($twig),
                'status' => $activityManager->getStatus(),
            ],
            200
        );
    }

    /**
     * @param string $id
     * @param string $cid
     * @param ActivityManager $activityManager
     * @param \Twig_Environment $twig
     * @return JsonResponse
     * @Route("/school/activity/external/{id}/slot/{cid}/manage/", name="external_activity_slot_manage")
     * @IsGranted("ROLE_PRINCIPAL")
     */
    public function externalActivitySlotManage($id = 'Add', $cid = 'ignore', ActivityManager $activityManager, \Twig_Environment $twig)
    {
        //if cid != ignore, then remove cid from collection
        $activity = $activityManager->setActivityType('external')->findActivity($id);

        if (intval($cid) > 0)
            $activityManager->removeActivitySlot($cid);

        $form = $this->createForm(ExternalActivityType::class, $activity);

        return new JsonResponse(
            [
                'content' => $this->renderView("School/external_activity_collection.html.twig",
                    [
                        'collection' => $form->get('activitySlots')->createView(),
                        'route' => 'external_activity_slot_manage',
                        'contentTarget' => 'activity_activitySlots_target',
                    ]
                ),
                'message' => $activityManager->getMessageManager()->renderView($twig),
                'status' => $activityManager->getStatus(),
            ],
            200
        );
    }

    /**
     * @Route("/activity/{id}/tutor/{cid}/manage/", name="activity_tutor_manage")
     * @IsGranted("ROLE_PRINCIPAL")
     * @param string $id
     * @param string $cid
     * @param ActivityManager $activityManager
     * @param \Twig_Environment $twig
     * @return JsonResponse
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function activityTutorManage($id = 'Add', $cid = 'ignore', ActivityManager $activityManager, \Twig_Environment $twig)
    {
        //if cid != ignore, then remove cid from collection
        $activity = $activityManager->setActivityType('activity')->findActivity($id);

        if (intval($cid) > 0)
            $activityManager->removeTutor($cid);

        switch ($activityManager->getActivityType()) {
            case 'class':
                $form = $this->createForm(FaceToFaceType::class, $activity);
                break;
            case 'roll':
                $form = $this->createForm(RollType::class, $activity);
                break;
            case 'external':
                $form = $this->createForm(ExternalActivityType::class, $activity);
                break;
        }

        return new JsonResponse(
            [
                'content' => $this->renderView("School/external_activity_collection.html.twig",
                    [
                        'collection' => $form->get('tutors')->createView(),
                        'route' => 'activity_tutor_manage',
                        'contentTarget' => 'tutorCollection',
                    ]
                ),
                'message' => $activityManager->getMessageManager()->renderView($twig),
                'status' => $activityManager->getStatus(),
            ],
            200
        );
    }

    /**
     * @Route("/activity/{id}/student/{cid}/manage/", name="activity_student_manage")
     * @IsGranted("ROLE_PRINCIPAL")
     * @param string $id
     * @param string $cid
     * @param ActivityManager $activityManager
     * @param \Twig_Environment $twig
     * @return JsonResponse
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function activityStudentManage($id = 'Add', $cid = 'ignore', ActivityManager $activityManager, \Twig_Environment $twig)
    {
        //if cid != ignore, then remove cid from collection
        $activity = $activityManager->setActivityType('activity')->findActivity($id);

        if (intval($cid) > 0)
            $activityManager->removeStudent($cid);

        switch ($activityManager->getActivityType()) {
            case 'class':
                $form = $this->createForm(FaceToFaceType::class, $activity);
                break;
            case 'roll':
                $form = $this->createForm(RollType::class, $activity);
                break;
            case 'external':
                $form = $this->createForm(ExternalActivityType::class, $activity);
                break;
        }
        return new JsonResponse(
            [
                'content' => $this->renderView("School/activity_collection.html.twig",
                    [
                        'collection' => $form->get('students')->createView(),
                        'route' => 'class_student_manage',
                        'contentTarget' => 'studentCollection',
                    ]
                ),
                'message' => $activityManager->getMessageManager()->renderView($twig),
                'status' => $activityManager->getStatus(),
            ],
            200
        );
    }
}