<?php
namespace App\Controller;

use App\Core\Form\SettingCreateType;
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
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
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
	public function create(Request $request, SettingManager $settingManager)
	{
		$form = $this->createForm(SettingCreateType::class);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$create = $request->request->get('create');
			$data   = Yaml::parse($create['setting']);
			$sm     = $settingManager;
			foreach ($data as $name => $values)
			{
				$setting = new Setting();
				$setting->setName($name);
				foreach ($values as $field => $value)
				{
					$b = 'set' . ucfirst($field);
					if ($field == 'value' && is_array($value))
						$value = Yaml::dump($value);
					$setting->$b($value);
				}
				$sm->createSetting($setting);
			}
		}

		return $this->render('Setting/create.html.twig',
			[
				'form'     => $form->createView(),
				'fullForm' => $form,
			]
		);
	}

	/**
	 * @param         $id
	 * @param Request $request
	 * @Route("/setting/edit/{id}/{closeWindow}", name="setting_edit")
	 * @IsGranted("ROLE_SYSTEM_ADMIN")
	 *
	 * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function edit($id, $closeWindow = null, Request $request, EntityManagerInterface $entityManager, SettingManager $settingManager)
	{
		$setting = $entityManager->getRepository(Setting::class)->find($id);

		if (is_null($setting))
			throw new \InvalidArgumentException('The System setting of identifier: ' . $id . ' was not found');

		if (is_null($setting->getRole())) $setting->setRole('ROLE_SYSTEM_ADMIN');

		$this->denyAccessUnlessGranted($setting->getRole(), null);


		$data  = $request->request->get('setting');
		$valid = true;

		if (!is_null($data))
		{
			switch ($setting->getType())
			{
				case 'array':
					try
					{
						$x = Yaml::parse($data['value']);
					}
					catch (\Exception $e)
					{
						$errorMsg = $e->getMessage();
						$valid    = false;
					}
					break;
			}
		}

		$form               = $this->createForm(SettingType::class, $setting, ['cancelURL' => $this->generateUrl('setting_manage')]);

		$form->handleRequest($request);

		if (! $valid)
		{
			$form->get('value')->addError(new FormError($errorMsg));
		}

		if ($form->isSubmitted() && $form->isValid())
		{
			$entityManager->persist($setting);
			$entityManager->flush();
			$session                       = $request->getSession();
			$settings                      = $session->get('settings', []);

			$settings[$setting->getName()] = $setting->getValue();

			$session->set('settings', $settings);

			if ($setting->getType() == 'image')
				return $this->redirectToRoute('setting_edit', ['id' => $id]);
		}

		return $this->render('Setting/edit.html.twig', [
				'form'       => $form->createView(),
				'fullForm'   => $form,
				'setting_id' => $setting->getId(),
				'closeWindow'=> $closeWindow,
			]
		);
	}

	/**
	 * @param                        $id
	 * @param Request                $request
	 * @param EntityManagerInterface $em
	 *
	 * @return RedirectResponse
	 * @Route("/setting/image/{id}/delete/", name="setting_delete_image")
	 * @IsGranted("ROLE_SYSTEM_ADMIN")
	 */
	public function deleteImage($id, Request $request, EntityManagerInterface $em, FlashBagManager $flashBagManager)
	{
		$setting = $em->getRepository(Setting::class)->find($id);

		if ($setting instanceof Setting)
		{
			$file = $setting->getValue();

			if (0 === strpos($file, 'uploads/'))
			{
				if (file_exists($file))
					unlink($file);

				$setting->setValue(null);
				$em->persist($setting);
				$em->flush();
				$session                       = $request->getSession();
				$settings                      = $session->get('settings', []);
				$settings[$setting->getName()] = null;

				$session->set('settings', $settings);

				$fs = new Filesystem();
				$fs->remove($this->get('kernel')->getCacheDir());
			}
		} else
		{
			$messages = new MessageManager('System');
			$messages->addMessage('warning', 'Core images will not be removed.');
			$flashBagManager->addMessages($messages);
		}

		return $this->redirectToRoute('setting_edit', ['id' => $id]);
	}

	/**
	 * @param         $name
	 * @param Request $request
	 *
	 * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 * @Route("setting/{name}/edit/{closeWindow}", name="setting_edit_name")
	 * @IsGranted("ROLE_SYSTEM_ADMIN")
	 */
	public function editName($name, $closeWindow = null,  Request $request, SettingRepository $settingRepository, EntityManagerInterface $entityManager, SettingManager $settingManager)
	{
		$setting = $settingRepository->findOneByName($name);

		if (is_null($setting)) throw new \InvalidArgumentException('The System setting of name: ' . $name . ' was not found');

		return $this->forward(SettingController::class.'::edit', ['id' => $setting->getId(), 'closeWindow' => $closeWindow]);
	}
}