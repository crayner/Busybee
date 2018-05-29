<?php
namespace App\Controller;

use App\Core\Form\SettingCreateType;
use App\Core\Form\SettingImportType;
use App\Core\Form\SettingType;
use App\Core\Manager\FlashBagManager;
use App\Core\Manager\MessageManager;
use App\Core\Manager\SettingManager;
use App\Entity\Setting;
use App\Pagination\SettingPagination;
use App\Repository\SettingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Yaml;

class SettingController extends Controller
{
	/**
	 * @param Request $request
	 *
	 * @Route("/setting/manage/", name="setting_manage")
	 * @IsGranted("ROLE_SYSTEM_ADMIN")
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function index(Request $request, SettingPagination $settingPagination, SettingManager $settingManager)
	{
		$settingPagination->injectRequest($request);

		$settingPagination->getDataSet();

		$form = $this->createForm(SettingImportType::class);

		$settingManager->handleImportRequest($form);

		return $this->render('Setting/manage.html.twig',
			array(
				'pagination' => $settingPagination,
				'form' => $this->renderView('Setting/import.html.twig', ['form' => $form->createView(),]),
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
	public function create(Request $request, SettingManager $settingManager)
	{
		$form = $this->createForm(SettingCreateType::class);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
		    $settingManager->createSettings();
		}

		return $this->render('Setting/create.html.twig',
			[
				'form'     => $form->createView(),
			]
		);
	}

	/**
	 * @param         $id
	 * @param Request $request
	 * @Route("/setting/manage/{id}/edit/{closeWindow}", name="setting_edit")
	 * @IsGranted("ROLE_SYSTEM_ADMIN")
	 * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function edit($id, $closeWindow = null, Request $request, SettingManager $settingManager)
	{
		$setting = $settingManager->getEntityManager()->getRepository(Setting::class)->find($id);

		if (is_null($setting))
			throw new \InvalidArgumentException('The System setting of identifier: ' . $id . ' was not found');

		$form = $this->createForm(SettingType::class, $setting, ['cancelURL' => $this->generateUrl('setting_manage')]);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
            $settingManager->getEntityManager()->persist($setting);
            $settingManager->getEntityManager()->flush();

			if ($setting->getType() == 'image')
				return $this->redirectToRoute('setting_edit', ['id' => $setting->getId(), 'closeWindow' => $closeWindow]);
		} else
		    $setting->setValid(false);

		$settingManager->injectSetting($setting);

		return $this->render('Setting/edit.html.twig', [
				'form'       => $form->createView(),
				'fullForm'   => $form,
				'closeWindow'=> $closeWindow,
                'settingManager' => $settingManager,
			]
		);
	}

	/**
	 * @param                        $id
	 * @param Request                $request
	 * @param EntityManagerInterface $em
	 *
	 * @return RedirectResponse
	 * @Route("/setting/image/{id}/delete/{closeWindow}", name="setting_delete_image")
	 */
	public function deleteImage($id, $closeWindow = null, Request $request, SettingManager $settingManager, FlashBagManager $flashBagManager)
	{
		$setting = $settingManager->find($id);

        $this->denyAccessUnlessGranted($setting->getRole() ?: 'ROLE_SYSTEM_ADMIN', null);

		if ($setting instanceof Setting)
		{
			$file = $setting->getValue();

			if (0 === strpos($file, 'uploads/'))  // check that
			{
				if (file_exists($file))
					unlink($file);

				$settingManager->set($setting->getName(), null);
			}
		} else
		{
			$messages = new MessageManager('System');
			$messages->addMessage('warning', 'Core images will not be removed.');
			$flashBagManager->addMessages($messages);
		}

        return $this->forward(SettingController::class.'::edit', ['id' => $id, 'closeWindow' => $closeWindow]);
	}

	/**
	 * @param         $name
	 * @param Request $request
	 *
	 * @return Response
	 * @Route("/setting/{name}/edit/{closeWindow}", name="setting_edit_name")
	 * @IsGranted("ROLE_SYSTEM_ADMIN")
	 */
	public function editName($name, $closeWindow = null,  Request $request, SettingRepository $settingRepository)
	{
		$setting = null;
		$original = $name;
		do
		{
			$setting = $settingRepository->findOneByName($name);

			if (is_null($setting))
			{
				$name = explode('.', $name);
				array_pop($name);
				$name = implode('.', $name);
			}

		} while (is_null($setting) && false !== mb_strpos($name, '.'));


		if (is_null($setting))
			throw new \InvalidArgumentException('The System setting of name: ' . $original . ' was not found');

		return $this->forward(SettingController::class.'::edit', ['id' => $setting->getId(), 'closeWindow' => $closeWindow]);
	}
}