<?php
namespace App\Controller;

use App\Calendar\Util\CalendarManager;
use App\Entity\Activity;
use App\Entity\CalendarGrade;
use App\Entity\Course;
use App\Entity\ExternalActivity;
use App\Entity\FaceToFace;
use App\Entity\Roll;
use App\Pagination\ClassPagination;
use App\Pagination\CoursePagination;
use App\Pagination\ExternalActivityPagination;
use App\Pagination\RollPagination;
use App\School\Form\ActivityType;
use App\School\Form\CalendarGradeType;
use App\School\Form\CourseType;
use App\School\Form\DaysTimesType;
use App\School\Form\ExternalActivityType;
use App\School\Form\FaceToFaceType;
use App\School\Util\ActivityManager;
use App\School\Util\CourseManager;
use App\School\Util\DaysTimesManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SchoolController extends Controller
{
	/**
	 * @param Request $request
	 * @Route("/school/school/days/times/", name="school_days_times")
	 * @return \Symfony\Component\HttpFoundation\Response
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
     * @Route("/school/school/course/list/", name="course_list")
     * @IsGranted("ROLE_REGISTRAR")
     * @param CoursePagination $coursePagination
     * @param Request $request
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
     * @Route("/school/school/course/{id}/edit/", name="course_edit")
     * @IsGranted("ROLE_REGISTRAR")
     */
    public function courseEdit($id, Request $request, ClassPagination $classPagination)
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
     * @param int|string $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function activityEdit(Request $request, $id = 'Add', $activityType, ActivityManager $activityManager)
    {
        $activity = $activityManager->setActivityType($activityType)->findActivity($id);

        $form = $this->createForm(ActivityType::class, $activity);
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager->persist($activity);
            $entityManager->flush();

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
     * @Route("/school/school/calendar_grade/{id}/students/", name="student_grade")
     * @IsGranted("ROLE_REGISTRAR")
     * @param Request $request
     * @param int|string $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function studentGrade($id, Request $request, CalendarManager $calendarManager)
    {
        $cg = $this->getDoctrine()->getRepository(CalendarGrade::class)->find($id);
        if (! $cg)
            $cg = new CalendarGrade();

        $form = $this->createForm(CalendarGradeType::class, $cg, ['calendar_data' => $calendarManager->getCurrentCalendar()]);

        return $this->render('activity_tutor_edit.html.twig',
            [
                'form' => $form->createView(),
                'fullForm' => $form,
            ]
        );
    }

    /**
     * @Route("/school/school/face_to_face/{id}/{course_id}/edit/", name="face_to_face_edit")
     * @IsGranted("ROLE_PRINCIPAL")
     * @param Request $request
     * @param int|string $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function faceToFaceEdit(Request $request, $id = 'Add', $course_id, EntityManagerInterface $entityManager)
    {
        $face = $entityManager->getRepository(FaceToFace::class)->find($id) ?: new FaceToFace();
        $form = $this->createForm(FaceToFaceType::class, $face);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager->persist($face);
            $entityManager->flush();

            if ($id === 'Add')
                return $this->redirectToRoute('face_to_face_edit', ['id' => $face->getId(), 'course_id' => $course_id]);

            $face->getStudents(true);
            $form = $this->createForm(FaceToFaceType::class, $face);
        }

        return $this->render('School/class_edit.html.twig',
            [
                'form' => $form->createView(),
                'course_id' => $course_id,
                'tabs' => $face->getTabs(),
            ]
        );
    }

    /**
     * @Route("/school/external/activity/list/", name="external_activity_list")
     * @IsGranted("ROLE_PRINCIPAL")
     * @param Request $request
     * @param RollPagination $activityPagination
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
     * @Route("/school/activity/external/{id}/edit/", name="external_activity_edit")
     * @IsGranted("ROLE_PRINCIPAL")
     * @param Request $request
     * @param int|string $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function externalActivityEdit(Request $request, $id = 'Add', ActivityManager $activityManager)
    {
        $activity = $activityManager->setActivityType('external')->findActivity($id);

        $form = $this->createForm(ExternalActivityType::class, $activity);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $activityManager->getEntityManager()->persist($activity);
            $activityManager->getEntityManager()->flush();

            if ($id === 'Add')
                return $this->forward(SchoolController::class . '::externalActivityEdit', ['id' => $activity->getId()]);
        }

        return $this->render('School/external_activity_edit.html.twig',
            [
                'form' => $form->createView(),
                'manager' => $activityManager,
                'fullForm' => $form,
            ]
        );
    }
}