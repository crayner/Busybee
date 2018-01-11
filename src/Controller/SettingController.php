<?php
namespace App\Controller;

use App\Pagination\SettingPagination;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SettingController extends Controller
{
	/**
	 * @param Request $request
	 *
	 * @Route("/setting/manage/", name="setting_manage")
	 * @IsGranted("ROLE_SYSTEM_ADMIN")
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function index(Request $request, SettingPagination $settingPagination)
	{
		$settingPagination->injectRequest($request);

		$settingPagination->getDataSet();

		return $this->render('Setting/manage.html.twig',
			array(
				'pagination' => $settingPagination,
			)
		);
	}

	/**
	 * This action is only used by the program developer.
	 *
	 * @Route("/setting/create/", name="setting_create")
	 * @IsGranted("ROLE_SYSTEM_ADMIN")
	 * @param Request $request
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function create(Request $request)
	{
	}

	/**
	 * @param         $id
	 * @param Request $request
	 * @Route("/setting/edit/{id}/", name="setting_edit")
	 * @IsGranted("ROLE_SYSTEM_ADMIN")
	 *
	 * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function edit($id, Request $request)
	{

	}
}