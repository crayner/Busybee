<?php
namespace App\Core\Subscriber;

use App\Core\Manager\SettingManager;
use App\Core\Type\ImageType;
use App\Core\Type\SettingChoiceType;
use App\Core\Type\TextType;
use App\Core\Type\TimeType;
use App\Core\Type\ToggleType;
use App\Core\Validator\Integer;
use App\Core\Validator\Regex;
use App\Core\Validator\Twig;
use App\Core\Validator\Yaml;
use App\Entity\Setting;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\RouterInterface;

class SettingSubscriber implements EventSubscriberInterface
{
	/**
	 * @var SettingManager 
	 */
	private $settingManager;

	/**
	 * @var RouterInterface
	 */
	private $router;

	/**
	 * SettingSubscriber constructor.
	 *
	 * @param SettingManager  $settingManager
	 * @param RouterInterface $router
	 */
	public function __construct(SettingManager $settingManager, RouterInterface $router)
	{
		$this->settingManager = $settingManager;
		$this->router = $router;
	}

	/**
	 * @return array
	 */
	public static function getSubscribedEvents()
	{
		// Tells the dispatcher that you want to listen on the form.pre_set_data
		// event and that the preSetData method should be called.
		return array(
			FormEvents::PRE_SET_DATA => 'preSetData',
		);
	}

	/**
	 * @param FormEvent $event
	 */
	public function preSetData(FormEvent $event)
	{
		$data = $event->getData();
		$form = $event->getForm();

		if ($data instanceof Setting)
		{


			$options = [
				'label' => 'system.setting.value.label',
			];
			$attr    = ['class' => 'changeSetting'];

			$constraints = [];
			if (! empty($data->getValidator()) && class_exists($data->getValidator())){
				$w = $data->getValidator();
				$constraints[] = new $w();
			}
			switch ($data->getType())
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


			switch ($data->getType())
			{
				case 'boolean':
					$form->add('value', ToggleType::class, array_merge($options, [
								'data'       => $this->settingManager->get($data->getName()),
								'help'       => 'system.setting.boolean.help',
							]
						)
					);
					break;
				case 'integer':
					$form->add('value', NumberType::class, array_merge($options, array(
								'data'        => $this->settingManager->get($data->getName()),
								'help'        => 'system.setting.integer.help',
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
								'data'        => $data->getValue(),
								'help'        => 'system.setting.image.help',
								'attr'        => array_merge($attr,
									array(
										'imageClass' => 'mediumLogo',
									)
								),
								'fileName'    => 'setting',
								'deletePhoto' => $this->router->generate('setting_delete_image', ['id' => $data->getId()]),
								'required'    => false,
								'fileName'    => 'setting_' . str_replace([' ', '.'], '_', $data->getName()),
							)
						)
					);
					break;
				case 'file':
					$form->add('value', TextType::class, array_merge($options, array(
								'data' => $this->settingManager->get($data->getName()),
								'attr' => array_merge($attr, []),
								'help' => 'system.setting.file.help',
							)
						)
					);
					break;
				case 'array':
					$form->add('value', TextareaType::class, array_merge($options, [
								'attr'        => array_merge($attr,
									array(
										'rows' => 8,
									)
								),
								'help'        => 'system.setting.array.help',
								'constraints' => array_merge(
									$constraints,
									array(
										new Yaml(),
									)
								),
								'data'        => \Symfony\Component\Yaml\Yaml::dump($this->settingManager->get($data->getName())),
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
								'help'        => 'system.setting.twig.help',
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
					break;
				case 'enum':
					$choice = $this->settingManager->getSettingEntity($data->getChoice());
					$form->add('value', SettingChoiceType::class, array_merge($options, [
								'setting_name'         => $choice->getName(),
								'setting_display_name' => $choice->getDisplayName(),
								'constraints'          => $constraints,
								'attr'                 => $attr,
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
								'data'        => $this->settingManager->get($data->getName()),
								'constraints' => $constraints,
								'attr'        => $attr,
							)
						)
					);
					break;
				default:
					dump($data);
					dump($form);
					die();
			}
		}
	}
}