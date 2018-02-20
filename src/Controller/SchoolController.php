<?php
namespace App\Controller;

use App\Entity\Course;
use App\Pagination\CoursePagination;
use App\Pagination\RollGroupPagination;
use App\Repository\RollGroupRepository;
use App\School\Form\CourseType;
use App\School\Form\DaysTimesType;
use App\School\Form\RollGroupType;
use App\School\Util\CourseManager;
use App\School\Util\DaysTimesManager;
use App\School\Util\RollGroupManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SchoolController extends Controller
{
	/**
	 * @param Request $request
	 * @Route("/school/days/times/", name="school_days_times")
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
     * @Route("/school/course/list/", name="course_list")
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
     * @Route("/school/course/{id}/edit/", name="course_edit")
     * @IsGranted("ROLE_REGISTRAR")
     */
    public function courseEdit($id, Request $request)
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

        return $this->render('School/course_edit.html.twig',
            [
                'form' => $form->createView(),
                'fullForm' => $form,
            ]
        );
    }
}