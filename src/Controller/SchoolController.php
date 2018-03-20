<?php
namespace App\Controller;

use App\Entity\Course;
use App\Pagination\ClassPagination;
use App\Pagination\CoursePagination;
use App\Pagination\ExternalActivityPagination;
use App\Pagination\RollPagination;
use App\School\Form\ActivityType;
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
     * @IsGranted("ROLE_REGISTRAR")
     * @param $id
     * @param Request $request
     * @param ClassPagination $classPagination
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function courseEdit($id, Request $request, ClassPagination $classPagination, CourseManager $courseManager)
    {
        $course = $this->getDoctrine()->getRepository(Course::class)->find($id) ?: new Course();

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
     * @Route("/school/activity/{id}/edit/{activityType}/", name="activity_edit")
     * @IsGranted("ROLE_REGISTRAR")
     * @param Request $request
     * @param string $id
     * @param $activityType
     * @param ActivityManager $activityManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function activityEdit(Request $request, $id = 'Add', $activityType, ActivityManager $activityManager)
    {
        $activity = $activityManager->setActivityType($activityType)->findActivity($id);

        $form = $this->createForm(ActivityType::class, $activity);
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid())
        {
            $activityManager->getEntityManager()->persist($activity);
            $activityManager->getEntityManager()->flush();

            if ($id === 'Add')
                return $this->redirectToRoute('activity_edit', ['id' => $activity->getId(), 'activityType' => $activityType]);
        }
    
        return $this->render('School/activity_edit.html.twig',
            [
                'form' => $form->createView(),
                'activity_type' => $activityType,
            ]
        );
    }

    /**
     * @Route("/school/face_to_face/{id}/{course_id}/edit/", name="face_to_face_edit")
     * @IsGranted("ROLE_PRINCIPAL")
     * @param Request $request
     * @param string $id
     * @param $course_id
     * @param ActivityManager $activityManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function faceToFaceEdit(Request $request, $id = 'Add', $course_id, ActivityManager $activityManager)
    {
        $activityManager->setActivityType('class');
        $face = $activityManager->findActivity($id);

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
                return $this->redirectToRoute('face_to_face_edit', ['id' => $face->getId(), 'course_id' => $course_id]);

            $face->getStudents(true);
            $form = $this->createForm(FaceToFaceType::class, $face);
        }

        return $this->render('School/class_edit.html.twig',
            [
                'form' => $form->createView(),
                'course_id' => $course_id,
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
     * @Route("/school/activity/external/{id}/edit/{refresh}", name="external_activity_edit")
     * @IsGranted("ROLE_PRINCIPAL")
     * @param Request $request
     * @param int|string $id
     * @param int $refresh
     * @param ActivityManager $activityManager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function externalActivityEdit(Request $request, $id = 'Add', $refresh = 0, ActivityManager $activityManager)
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
                    return $this->forward(SchoolController::class . '::externalActivityEdit', ['id' => $activity->getId()]);
            }
        }
        return $this->render('School/external_activity_edit.html.twig',
            [
                'form' => $form->createView(),
                'manager' => $activityManager,
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
                        'contentTarget' => 'studentCollection',
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
                        'contentTarget' => 'slotCollection',
                    ]
                ),
                'message' => $activityManager->getMessageManager()->renderView($twig),
                'status' => $activityManager->getStatus(),
            ],
            200
        );
    }

    /**
     * @Route("/school/class/{id}/tutor/{cid}/manage/", name="class_tutor_manage")
     * @IsGranted("ROLE_PRINCIPAL")
     * @param string $id
     * @param string $cid
     * @param ActivityManager $activityManager
     * @param \Twig_Environment $twig
     * @return JsonResponse
     */
    public function classTutorManage($id = 'Add', $cid = 'ignore', ActivityManager $activityManager, \Twig_Environment $twig)
    {
        //if cid != ignore, then remove cid from collection
        $activity = $activityManager->setActivityType('class')->findActivity($id);

        if (intval($cid) > 0)
            $activityManager->removeTutor($cid);

        $form = $this->createForm(FaceToFaceType::class, $activity);

        return new JsonResponse(
            [
                'content' => $this->renderView("School/external_activity_collection.html.twig",
                    [
                        'collection' => $form->get('tutors')->createView(),
                        'route' => 'class_tutor_manage',
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
     * @Route("/school/class/{id}/student{cid}/manage/", name="class_student_manage")
     * @IsGranted("ROLE_PRINCIPAL")
     * @param string $id
     * @param string $cid
     * @param ActivityManager $activityManager
     * @param \Twig_Environment $twig
     * @return JsonResponse
     */
    public function classStudentManage($id = 'Add', $cid = 'ignore', ActivityManager $activityManager, \Twig_Environment $twig)
    {
        //if cid != ignore, then remove cid from collection
        $activity = $activityManager->setActivityType('class')->findActivity($id);

        if (intval($cid) > 0)
            $activityManager->removeStudent($cid);

        $form = $this->createForm(FaceToFaceType::class, $activity);

        return new JsonResponse(
            [
                'content' => $this->renderView("School/external_activity_collection.html.twig",
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