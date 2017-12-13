<?php
namespace App\Core\Manager;

use App\Entity\Setting;
use App\Entity\User;
use App\Repository\SettingRepository;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Setting Manager
 *
 * @version    6th September 2017
 * @since      20th October 2016
 * @author     Craig Rayner
 */
class SettingManager implements ContainerAwareInterface
{
	/**
	 * @var Setting
	 */
	private $setting;

	/**
	 * @var array
	 */
	private $settingCache;

	/**
	 * @var User
	 */
	private $currentUser;

	/**
	 * @var array
	 */
	private $settings;

	/**
	 * @var bool
	 */
	private $settingExists = false;

	/**
	 * @var projectDir
	 */
	private $projectDir;

	/**
	 * @var SettingRepository
	 */
	private $settingRepo;

	/**
	 * @var ContainerInterface
	 */
	private $container;

	/**
	 * @var MessageManager
	 */
	private $messages;

	/**
	 * @var  Session
	 */
	private $session;

	private $authorisatiion;
	/**
	 * SettingManager constructor.
	 *
	 * @param ContainerInterface $container
	 */
	public function __construct(SettingRepository $sr, myContainer $container, Authorisation $authorisatiion)
	{
		$this->session = new Session();
		if ($this->session->isStarted())
		{
			$this->settings     = $this->session->get('settings');
			$this->settingCache = $this->session->get('settingCache');
		} else {
			$this->settings     = [];
			$this->settingCache = [];
		}

		$this->settingRepo  = $sr;
		$this->projectDir   = $container->getParameter('kernel.project_dir');

		$this->container = $container;

		$this->authorisatiion = $authorisatiion;
		$this->messages = new MessageManager('SystemBundle');
	}

	/**
	 * @{inheritdoc}
	 * @return User
	 */
	public function getCurrentUser()
	{
		return $this->currentUser;
	}

	/**
	 * @{inheritdoc}
	 */
	public function setCurrentUser(User $user = null): SettingManager
	{
		$this->currentUser = $user;

		return $this;
	}

	/**
	 * get Form Array Data
	 *
	 * @version 1st Novenber 2016
	 * @since   1st Novenber 2016
	 *
	 * @param   string $name
	 * @param   mixed  $default
	 * @param   array  $options
	 *
	 * @return  mixed    Value
	 */
	public function getFormArrayData($name, $default = null, $options = array())
	{
		$x = $this->getSetting($name, $default, $options);
		$y = array();
		foreach ($x as $display => $value)
		{
			$w                = array();
			$w['keyValue']    = $value;
			$w['displayName'] = $display;
			$y[]              = $w;
		}
		$w                = array();
		$w['keyValue']    = '';
		$w['displayName'] = '';
		$y['new']         = $w;

		return $y;
	}

	/**
	 * get Setting
	 *
	 * @version    18th November 2016
	 * @since      20th October 2016
	 *
	 * @param    string $name
	 * @param    mixed  $default
	 * @param    array  $options
	 *
	 * @return    mixed    Value
	 * @throws  \Exception
	 */
	public function getSetting($name, $default = null, $options = array())
	{
		$name = strtolower($name);

		if (isset($this->settings[$name]) && $this->settingCache[$name] > new \DateTime('-15 Minutes') && empty($options))
		{
			$this->settingExists = true;

			return $this->settings[$name];
		}

		$this->settingExists = false;
		$flip                = false;
		if (substr($name, -6) === '._flip')
		{
			$flip = true;
			$name = str_replace('._flip', '', $name);
		}

		try
		{
			$this->setting = $this->getSettingEntity($name);
		}
		catch (\Exception $e)
		{
			return $default;
		}

		if (is_null($this->setting) || empty($this->setting->getName()))
		{
			if (false === strpos($name, '.'))
				return $default;
			$name = explode('.', $name);
			$last = end($name);
			array_pop($name);
			$value = $this->getSetting(implode('.', $name), $default, $options);

			if (is_array($value))
			{
				$x = [];
				foreach ($value as $name => $data)
					$x[strtolower($name)] = $data;
				$value = $x;
			}

			if (is_array($value) && isset($value[$last]))
				return $value[$last];

			return $default;
		}

		$this->settingExists = true;

		switch ($this->setting->getType())
		{
			case 'system':
			case 'regex':
			case 'text':
				return $this->writeSettingToSession($name, $this->setting->getValue());
				break;
			case 'string':
				return $this->writeSettingToSession($name, strval(mb_substr($this->setting->getValue(), 0, 25)));
				break;
			case 'file':
			case 'image':
				$value   = $this->setting->getValue();
				$webPath = realpath($this->projectDir . '/web/');
				if (!file_exists($webPath . '/' . $value))
					$value = $default;

				return $this->writeSettingToSession($name, $value);
				break;
			case 'twig':
				try
				{
					return $this->container->get('twig')->createTemplate($this->setting->getValue())->render($options);
				}
				catch (\Twig_Error_Runtime $e)
				{
					return $this->getContainer()->get('translator')->trans('setting.twig.error', ['%name%' => $this->setting->getName(), '%value%' => $this->setting->getValue(), '%error%' => $e->getMessage(), '%options%' => implode(', ', $options)], 'SystemBundle');
				}
				catch (\Twig_Error_Loader $e)
				{
					return $this->getContainer()->get('translator')->trans('setting.twig.error', ['%name%' => $this->setting->getName(), '%value%' => $this->setting->getValue(), '%error%' => $e->getMessage(), '%options%' => implode(', ', $options)], 'SystemBundle');
				}
				break;
			case 'boolean':
				return $this->writeSettingToSession($name, (bool) $this->setting->getValue());
				break;
			case 'time':
				return $this->writeSettingToSession($name, $this->setting->getValue());
				break;
			case 'array':
				if ($flip)
					return $this->writeSettingToSession($name . '_flip', array_flip($this->setting->getValue()));
				else
					return $this->writeSettingToSession($name, $this->setting->getValue());
				break;
			case 'integer':
				return $this->writeSettingToSession($name, intval($this->setting->getValue()));
				break;
			default:
				throw new Exception('The Setting Type (' . $this->setting->getType() . ') has not been defined for ' . $name . '.');
		}
	}

	/**
	 * set Setting
	 *
	 * @version    9th November 2017
	 * @since      21st October 2016
	 *
	 * @param    string $name
	 * @param    mixed  $value
	 *
	 * @return    SettingManager
	 */
	public function setSetting($name, $value)
	{
		$name          = strtolower($name);
		$this->setting = $this->settingRepo->findOneByName($name);
		if (is_null($this->setting) || empty($this->setting->getName()))
			return $this;
		if (true !== $this->authorisatiion->redirectAuthorisation($this->setting->getRole(), 'security.authorisation.setting', ['settingName' => $this->setting->getName(), 'role%' => $this->setting->getRole()])) return $this;
		switch ($this->setting->getType())
		{
			case 'string':
				$value = strval(mb_substr($value, 0, 25));
				break;
			case 'integer':
				$value = intval($value);
				break;
			case 'regex':
				if (empty($value)) $value = '/^/';
				$test = preg_match($value, 'qwlrfhfri$wegtiwebnf934htr 5965tb'); //Just rubbish to test that the regex is a valid format.
				break;
			case 'time':
				if (!empty($value) && $value instanceof \DateTime)
					$value = $value->format('H:i');
				break;
			case 'image':
			case 'file':
			case 'text':
			case 'system':
				break;
			case 'twig':
				if (is_null($value)) $value = '{{ empty }}';
				try
				{
					$x = $this->container->get('twig')->createTemplate($value)->render(array());
				}
				catch (\Twig_Error_Syntax $e)
				{
					throw $e;
				}
				catch (\Twig_Error_Runtime $e)
				{
					// Ignore Runtime Errors
				}
				break;
			case 'boolean':
				$value = (bool) $value;
				break;
			case 'array':
				break;
			default:
				throw new Exception('The Setting Type (' . $this->setting->getType() . ') has not been defined.');
		}

		$this->setting->setValue($value);
		$em = $this->container->get('doctrine')->getManager();
		$em->persist($this->setting);
		$em->flush();
		switch ($this->setting->getType())
		{
			case 'twig':
				break;
			default:
				$this->writeSettingToSession($name, $value);
		}

		return $this;
	}

	/**
	 * Write Setting to Session
	 *
	 * @param $name
	 * @param $value
	 *
	 * @return mixed
	 */
	private function writeSettingToSession($name, $value)
	{
		$this->settings[$name]     = $value;
		$this->settingCache[$name] = new \DateTime('now');

		if ($this->session->isStarted()){
			$this->session->set('settings', $this->settings);
			$this->session->set('settingCache', $this->settingCache);
		}

		return $value;
	}

	/**
	 * @return myContainer
	 */
	public function getContainer(): myContainer
	{
		return $this->container;
	}

	/**
	 * Set Container
	 *
	 * @param ContainerInterface $container
	 *
	 * @return $this
	 */
	public function setContainer(ContainerInterface $container = null)
	{
		$this->container = $container;

		return $this;
	}

	/**
	 * set Form Array Data
	 *
	 * @version    1st Novenber 2016
	 * @since      1st Novenber 2016
	 *
	 * @param    array $value
	 *
	 * @return    array
	 */
	public function setFormArrayData($value)
	{
		$result = array();
		foreach ($value as $w)
		{
			if (!empty($w['keyValue']))
				$result[$w['displayName']] = $w['keyValue'];
		}

		return $result;
	}

	/**
	 * increment Version
	 *
	 * @version    20th October 2016
	 * @since      20th October 2016
	 *
	 * @param    string $version
	 *
	 * @return    string Version
	 */
	public function incrementVersion($version)
	{
		$v = explode('.', $version);
		if (!isset($v[2])) $v[2] = 0;
		if (!isset($v[1])) $v[1] = 0;
		if (!isset($v[0])) $v[0] = 0;
		while (count($v) > 3)
			array_pop($v);
		$v[2]++;
		if ($v[2] > 99)
		{
			$v[2] = 0;
			$v[1]++;
		}
		if ($v[1] > 9)
		{
			$v[1] = 0;
			$v[0]++;
		}
		$v[2] = str_pad($v[2], 2, '00', STR_PAD_LEFT);

		return implode('.', $v);
	}

	/**
	 * get Choices
	 *
	 * @version    15th November 2016
	 * @since      15th November 2016
	 *
	 * @param    string $version
	 *
	 * @return    array
	 */
	public function getChoices($choice)
	{
		if (0 === strpos($choice, 'parameter.'))
		{
			$name = substr($choice, 10);
			if (false === strpos($name, '.'))
				$list = $this->getParameter($name);
			else
			{
				$name = explode('.', $name);
				$list = $this->getParameter($name[0]);
				array_shift($name);
				while (!empty($name))
				{
					$key  = reset($name);
					$list = $list[$key];
					array_shift($name);
				}
			}
		}
		else
			$list = $this->get($choice);

		return $list;
	}

	/**
	 * get Setting
	 *
	 * @version    31st October 2016
	 * @since      20th October 2016
	 *
	 * @param    string $name
	 * @param    mixed  $default
	 * @param    array  $options
	 *
	 * @return    mixed    Value
	 */
	public function get($name, $default = null, $options = array())
	{
		$name                = strtolower($name);
		$this->settingExists = false;
		if (isset($this->settings[$name]) && empty($options))
		{
			$this->settingExists = true;

			return empty($this->settings[$name]) ? $default : $this->settings[$name];
		}
		$value = $this->getSetting($name, $default, $options);
		if ($this->settingExists && empty($options))
		{
			$this->settings[$name]     = $value;
			$this->settingCache[$name] = new \DateTime('now');
		}

		return $value;
	}


	/**
	 * get Setting Entity
	 *
	 * @version    24th November 2017
	 * @since      24th November 2017
	 *
	 * @param    string $name
	 *
	 * @return    Setting|null
	 */
	public function getSettingEntity($name): ?Setting
	{
		$name          = strtolower($name);
		$this->setting = $this->settingRepo->findOneByName($name);

		return $this->setting;
	}


	/**
	 * Build Form
	 *
	 * @version    30th November 2016
	 * @since      30th November 2016
	 *
	 * @param    form  $value
	 * @param    array $value
	 *
	 * @return    form
	 */
	public function buildForm($form, $settings)
	{
		foreach ($settings as $name => $setting)
		{
			$details                = $this->settingRepo->findOneByName($setting['setting']);
			$type                   = null;
			$options                = array(
				'data'              => $details->getValue(),
				'label'             => $name . ' ( ' . $details->getDisplayName() . ' )',
				'attr'              => array(
					'help' => $details->getDescription(),
				),
				'validation_groups' => array('Default'),
			);
			$options['constraints'] = array();
			if (isset($setting['blank']) && $setting['blank']) $options['required'] = false;

			if (isset($setting['length'])) $options['attr']['maxLength'] = $setting['length'];
			if (isset($setting['minLength'])) $options['attr']['minLength'] = $setting['minLength'];

			if (!empty($details->getChoice()))
			{
				if (0 === strpos($details->getChoice(), 'parameter.'))
				{
					$options['choices'] = $this->getParameter(str_replace('parameter.', '', $details->getChoice()));
				}
				else
				{
					$options['choices'] = $this->get($details->getChoice());
				}
				$type = ChoiceType::class;
			}
			if (!is_null($validator = $details->getValidator()))
			{
				$validator = explode(',', $validator);
				foreach ($validator as $w)
					switch ($w)
					{
						case 'phone.validator':
							array_push($options['constraints'], new Phone(array('groups' => 'Default')));
							break;
						case 'busybee_facility_institute.validator.constraints.institute_name_validator':
							array_push($options['constraints'], new InstituteName(array('groups' => 'Default')));
							break;
						default:
							throw new \Exception('I cannot handle ' . $w);
					}
			}
			$form->add(str_replace('.', '_', $details->getName()), $type, $options);
		}

		return $form;
	}

	/**
	 * delete Setting
	 *
	 * @version    14th July 2017
	 * @since      21st October 2016
	 *
	 * @param    Setting /String
	 *
	 * @return    SettingManager
	 */
	public function deleteSetting($setting): SettingManager
	{
		if (!$setting instanceof Setting)
		{
			$this->setting = $this->settingRepo->findOneByName($setting);
			if ($this->setting instanceof Setting)
				$setting = $this->setting;
			else
				return $this;
		}
		$om = $this->container->get('doctrine')->getManager();
		$om->remove($setting);
		$om->flush();

		$this->clearSessionSetting($setting->getName());
		$this->setting = null;

		return $this;
	}

	/**
	 * Clear Session Setting
	 *
	 * @param $name
	 *
	 * @return SettingManager
	 */
	private function clearSessionSetting($name)
	{
		if (empty($this->settings[$name]))
			return $this;
		unset($this->settings[$name], $this->settingCache[$name]);
		$this->container->get('session')->set('settings', $this->settings);
		$this->container->get('session')->set('settingCache', $this->settingCache);

		return $this;
	}

	/**
	 * create Setting
	 *
	 * @version 5th April 2017
	 * @since   21st October 2016
	 *
	 * @param   Setting
	 *
	 * @return  SettingManager
	 */
	public function createSetting(Setting $setting)
	{
		if (!$this->settingExists($setting->getName()))
		{
			$em = $this->container->get('doctrine')->getManager();
			if ($setting->getType() == 'array' && is_array($setting->getValue()))
				$setting->setValue(Yaml::dump($setting->getValue()));

			$em->persist($setting);
			$em->flush();
		}
		elseif (!empty($setting->getValue()))
		{
			$this->set($setting->getName(), $setting->getValue());
		}
		else
		{
			$this->get($setting->getName());
		}

		return $this;
	}

	/**
	 * @param $name
	 *
	 * @return bool
	 */
	public function settingExists($name)
	{
		$this->get($name);

		return $this->settingExists;
	}

	/**
	 * set Setting
	 *
	 * @version 31st October 2016
	 * @since   21st October 2016
	 *
	 * @param   string $name
	 * @param   mixed  $value
	 *
	 * @return  mixed
	 */
	public function set($name, $value)
	{
		$name = strtolower($name);

		return $this->setSetting($name, $value);
	}

	/**
	 * @param   $name
	 *
	 * @return  string
	 */
	public function getLikeSettingNames($name)
	{
		$query   = $this->settingRepo->createQueryBuilder('s')
			->select(['s.name', 's.displayName'])
			->where('s.name LIKE :name1')
			->orWhere('s.description LIKE :name2')
			->orWhere('s.displayName LIKE :name3')
			->setParameter('name1', '%' . $name . '%')
			->setParameter('name2', '%' . $name . '%')
			->setParameter('name3', '%' . $name . '%')
			->orderBy('s.name')
			->getQuery();
		$results = $query->getResult();
		if (empty($results))
			return '';
		$return = ' Did you mean ';

		foreach ($results as $setting)
		{
			$return .= $setting['name'] . ' (' . $setting['displayName'] . ') ';
		}

		return $return;
	}

	/**
	 * Has Setting
	 *
	 * @version 30th November 2016
	 * @since   21st October 2016
	 *
	 * @param   string $name
	 * @param   mixed  $value
	 *
	 * @return  boolean
	 */
	public function has($name, $clearCache = false)
	{
		$name = strtolower($name);
		if ($clearCache)
		{
			if (isset($this->settings[$name]))
			{
				unset($this->settings[$name]);
				$this->container->get('session')->set('settings', $this->settings);
			}
			if (isset($this->settingCache[$name]))
			{
				unset($this->settingCache[$name]);
				$this->container->get('session')->set('settingCache', $this->settingCache);
			}
		}

		return $this->settingExists($name);
	}

	/**
	 * Get parameter
	 *
	 * @param   string $name
	 * @param   mixed  $default
	 *
	 * @return  mixed
	 */
	public function getParameter($name, $default = null)
	{
		if (!$this->hasParameter($name))
			return $default;

		return $this->container->getParameter($name);
	}

	/**
	 * @return MessageManager
	 */
	public function getMessages(): MessageManager
	{
		return $this->messages;
	}


	/**
	 * load Setting File
	 *
	 * @version 12th March 2017
	 * @since   12th March 2017
	 * @return  array
	 * @throws  ParseException
	 */
	private function loadSettingFile($fName)
	{
		try
		{
			$data = Yaml::parse(file_get_contents($fName));
		}
		catch (ParseException $e)
		{
			$this->container->get('session')->getFlashBag()->add('error', $this->container->get('translator')->trans('updateDatabase.failure', array('%fName%' => $fName), 'SystemBundle'));

			return [];
		}

		return $data;
	}

	/**
	 * Has parameter
	 *
	 * @param   string $name
	 * @param   mixed  $default
	 *
	 * @return  mixed
	 */
	public function hasParameter($name)
	{
		return $this->container->hasParameter($name);
	}

	/**
	 * @param $name
	 */
	public function clear($name)
	{
		$this->clearSessionSetting($name);
		$this->set($name, null);
	}

}