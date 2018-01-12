<?php
namespace App\Controller;

use App\Core\Form\SettingType;
use App\Core\Manager\FlashBagManager;
use App\Core\Manager\MessageManager;
use App\Core\Manager\SettingManager;
use App\Core\Type\ImageType;
use App\Core\Type\SettingChoiceType;
use App\Core\Type\TextType;
use App\Core\Type\TimeType;
use App\Core\Validator\Integer;
use App\Core\Validator\Regex;
use App\Core\Validator\Twig;
use App\Entity\Setting;
use App\Pagination\SettingPagination;
use App\Repository\SettingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
	public function edit($id, Request $request, EntityManagerInterface $entityManager, SettingManager $settingManager)
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

		$setting->cancelURL = $this->generateUrl('setting_manage');
		$form               = $this->createForm(SettingType::class, $setting);

		$options = array(
			'label' => 'system.setting.value.label',
		);
		$attr    = array('class' => 'changeSetting');

		$constraints = array();
		if (!is_null($setting->getValidator()))
			$constraints[] = $this->get($setting->getValidator());
		switch ($setting->getType())
		{
			case 'array':
				$constraints[] = new \App\Core\Validator\Yaml();
				break;
			case 'twig':
				$constraints[] = new Twig();
				break;
			case 'regex':
				$constraints[] = new Regex();
				break;
		}

		if (count($constraints) > 0) $options['constraints'] = $constraints;

		switch ($setting->getType())
		{
			case 'boolean':
				$form->add('value', ToggleType::class, array_merge($options, array(
							'data' => $settingManager->get($setting->getName()),
							'attr' => array(
								'help' => 'system.setting.help.boolean',
							)
						)
					)
				);
				break;
			case 'integer':
				$form->add('value', NumberType::class, array_merge($options, array(
							'data'        => $settingManager->get($setting->getName()),
							'attr'        => array(
								'help' => 'system.setting.integer.help',
							),
							'constraints' => array_merge(
								$constraints,
								array(
									new Integer(),
								)
							),
						)
					)
				);
				break;
			case 'image':
				$form->add('value', ImageType::class, array_merge($options, array(
							'data'        => $setting->getValue(),
							'attr'        => array_merge($attr,
								array(
									'help'       => 'system.setting.help.image',
									'imageClass' => 'mediumLogo',
								)
							),
							'fileName'    => 'setting',
							'deletePhoto' => $this->generateUrl('setting_delete_image', ['id' => $id]),
							'required'    => false,
						)
					)
				);
				break;
			case 'file':
				$form->add('value', TextType::class, array_merge($options, array(
							'data' => $settingManager->get($setting->getName()),
							'attr' => array_merge($attr,
								array(
									'help' => 'system.setting.help.file',
								)
							),
						)
					)
				);
				break;
			case 'array':
				$form->add('value', TextareaType::class, array_merge($options, [
							'attr'        => array_merge($attr,
								array(
									'help' => 'system.setting.help.array',
									'rows' => 8,
								)
							),
							'constraints' => array_merge(
								$constraints,
								array(
									new \App\Core\Validator\Yaml(),
								)
							),
							'data'        => Yaml::dump($settingManager->get($setting->getName())),
						]
					)
				);
				break;
			case 'twig':
				$form->add('value', TextareaType::class, array_merge($options, array(
							'attr'        => array_merge($attr,
								[
									'rows' => 5,
								]
							),
							'help' => 'system.setting.twig.help',
							'constraints' => array_merge(
								$constraints,
								array(
									new Twig(),
								)
							),
						)
					)
				);
				break;
			case 'system':
				$form->add('value', TextType::class, array_merge($options, array(
							'attr'        => array_merge($attr,
								[
									'maxLength' => 25,
									'readonly'  => 'readonly',
								]
							),
							'constraints' => $constraints,
						)
					)
				);
				break;
			case 'string':
				if (is_null($setting->getChoice()))
					$form->add('value', TextType::class, array_merge($options, array(
								'attr'        => array_merge($attr,
									array(
										'maxLength' => 25,
									)
								),
								'constraints' => $constraints,
							)
						)
					);
				else
					$choice = $settingManager->getSettingEntity($setting->getChoice());
					$form->add('value', SettingChoiceType::class, array_merge($options, [
								'setting_name' => $choice->getName(),
								'setting_display_name' => $choice->getDisplayName(),
								'constraints'  => $constraints,
								'attr'         => $attr,
							]
						)
					);
				break;
			case 'regex':
				$form->add('value', TextareaType::class, array_merge($options, array(
							'attr'        => array_merge($attr,
								array(
									'rows' => 5,
								)
							),
							'constraints' => array_merge(
								$constraints,
								array(
									new Regex(),
								)
							),
						)
					)
				);
				break;
			case 'text':
				$form->add('value', TextType::class, array_merge($options, array(
							'constraints' => $constraints,
							'attr'        => $attr,
						)
					)
				);
				break;
			case 'time':
				$form->add('value', TimeType::class, array_merge($options, array(
							'data'        => $settingManager->get($setting->getName()),
							'constraints' => $constraints,
							'attr'        => $attr,
						)
					)
				);
				break;
			default:
				throw new \InvalidArgumentException(sprintf("The setting type %s has not been defined.", $setting->getType()));
		}

		$form->handleRequest($request);

		if (!$valid)
		{
			$form->get('value')->addError(new FormError($errorMsg));
		}

		if ($form->isSubmitted() && $form->isValid())
		{
			$em = $this->get('doctrine')->getManager();
			$em->persist($setting);
			$em->flush();
			$session                       = $this->get('session');
			$settings                      = $session->get('settings', []);

			$settings[$setting->getName()] = $setting->getValue();

			$session->set('settings', $settings);

			if ($setting->getType() == 'image')
				return $this->redirectToRoute('setting_edit', array('id' => $id));
		}

		return $this->render('Setting/edit.html.twig', [
				'form'       => $form->createView(),
				'fullForm'   => $form,
				'setting_id' => $setting->getId(),
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

			if (false === strpos($file, 'img/'))
			{
				if (file_exists($file))
					unlink($file);

				$setting->setValue(null);
				$em = $this->get('doctrine')->getManager();
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
	 * @Route("setting/{name}/edit/", name="setting_edit_name")
	 * @IsGranted("ROLE_SYSTEM_ADMIN")
	 */
	public function editNameAction($name, Request $request, SettingRepository $settingRepository, EntityManagerInterface $entityManager, SettingManager $settingManager)
	{
		$setting = $settingRepository->findOneByName($name);

		if (is_null($setting)) throw new \InvalidArgumentException('The System setting of name: ' . $name . ' was not found');

		return $this->forward(SettingController::class.'::edit', ['id' => $setting->getId()]);
	}
}