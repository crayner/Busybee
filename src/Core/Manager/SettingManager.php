<?php
namespace App\Core\Manager;

use App\Core\Exception\Exception;
use App\Entity\Setting;
use App\Repository\SettingRepository;
use Hillrange\Security\Entity\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

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
	 * @var array
	 */
	private $settingExists;

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
	 * @var  SessionInterface
	 */
	private $session;

	/**
	 * @var AuthorizationCheckerInterface
	 */
	private $authorisation;

	/**
	 * @var \Twig_Environment
	 */
	private $twig;

	/**
	 * @var MessageManager
	 */
	private $messageManager;

	/**
	 * SettingManager constructor.
	 *
	 * @param SettingRepository             $sr
	 * @param ContainerInterface            $container
	 * @param AuthorizationCheckerInterface $authorisation
	 * @param \Twig_Environment             $twig
	 * @param MessageManager                $messageManager
	 * @param SessionInterface              $session
	 */
	public function __construct(SettingRepository $sr, ContainerInterface $container, AuthorizationCheckerInterface $authorisation, \Twig_Environment $twig, MessageManager $messageManager, SessionInterface $session)
	{
		$this->session = $session;

		$this->settings     = null;
		$this->settingCache = null;

		$this->settingRepo  = $sr;
		$this->projectDir   = $container->getParameter('kernel.project_dir');

		$this->container = $container;

		$this->authorisation = $authorisation;

		$this->twig = $twig;
		$this->messageManager = $messageManager;
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

		if (! $this->authorisation->isGranted($this->setting->getRole(), $this->setting))
			return $this;

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
            case 'array':
				break;
			case 'twig':
				if (is_null($value)) $value = '{{ empty }}';
				try
				{
					$x = $this->twig->createTemplate($value)->render([]);
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
			default:
				throw new Exception('The Setting Type (' . $this->setting->getType() . ') has not been defined.');
		}

		$this->setting->setValue($value);
		$em = $this->container->get('doctrine')->getManager();
		$em->persist($this->setting);
		$em->flush();
		$this->writeSettingToSession($name);

		return $this;
	}

	/**
	 * Write Setting to Session
	 *
	 * @param $name
	 *
	 * @return void
	 */
	private function writeSettingToSession($name)
	{
		if (null === $this->setting || null === $this->setting->getType() || $this->setting->getType() === 'twig')
			return;


		$this->settings[$name]     = $this->setting;
		$this->settingCache[$name] = new \DateTime('now');
		$this->settingExists[$name] = true;

		if ($this->session->isStarted()){
			$this->session->set('settings', $this->settings);
			$this->session->set('settingCache', $this->settingCache);
			$this->session->set('settingExists', $this->settingExists);
		}
	}

	/**
	 * @return myContainer
	 */
	public function getContainer(): ?ContainerInterface
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
	public function get($name, $default = null, $options = [])
	{
		return $this->getSetting($name, $default, $options);
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
	public function getSetting($name, $default = null, $options = [])
	{
		$this->loadSettingsFromSession();

		$name = strtolower($name);
		$flip                = false;
		if (substr($name, -6) === '._flip')
		{
			$flip = true;
			$name = str_replace('._flip', '', $name);
		}

		$this->settingExists[$name] = empty($this->settingExists[$name]) ? false : $this->settingExists[$name] ;

		if (! empty($this->settings[$name]) && (empty($this->settingCache[$name]) || $this->settingCache[$name] > new \DateTime('-15 Minutes') && empty($options)))
		{
			$this->settingExists[$name] = true;

			$this->setting = $this->settings[$name];
		} else {

			$this->settingExists[$name] = false;

			$this->setting = $this->getSettingEntity($name);
		}

		if ($this->settingExists[$name] && $name === $this->setting->getName() && $this->setting->getType() !== 'twig') {
			if ($flip && $this->setting->getType() === 'array')
				return array_flip($this->setting->getValue());

			return empty($this->setting->getValue()) ? $default : $this->setting->getValue() ;
		} elseif (! $this->settingExists[$name] && $this->setting->getType() === 'array') {
			return $this->getSettingArray($name, $default);
		} elseif ($this->settingExists[$name] && $name === $this->setting->getName() && $this->setting->getType() === 'twig') {
			try
			{
				return $this->twig->createTemplate($this->setting->getValue())->render($options);
			}
			catch (\Twig_Error_Loader $e)
			{
				$this->messageManager->add('danger','setting.twig.error', ['%name%' => $this->setting->getName(), '%value%' => $this->setting->getValue(), '%error%' => $e->getMessage(), '%options%' => implode(', ', $options)], 'System');
				return null;
			}
			catch (\Twig_Error_Runtime $e)
			{
				if (! preg_match('/^(Variable )(.+)( does not exist in)(.+)( at line )/', $e->getMessage()))
					$this->messageManager->add('danger','setting.twig.error', ['%name%' => $this->setting->getName(), '%value%' => $this->setting->getValue(), '%error%' => $e->getMessage(), '%options%' => implode(', ', $options)], 'System');
				return null;

			}

		}
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
		$name          = str_replace('._flip', '', strtolower($name));

		$this->setting = $this->settingRepo->loadOneByName($name);
		if (is_null($this->setting) && strpos($name, '.') !== false)
		{
			$x = explode('.', $name);
			array_pop($x);
			$name = implode('.', $x);
			$this->getSettingEntity($name);
		}
		else
		{
			$this->settings[$name] = $this->setting;
			$this->writeSettingToSession($name);
			$this->settingExists[$name] = true;
		}


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
		$this->settingExists[$name] = false;
		$this->session->set('settings', $this->settings);
		$this->session->set('settingCache', $this->settingCache);
		$this->session->set('settingExists', $this->settingExists);

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
dump($setting);
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

		return $this->settingExists[$name];
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
		if ($this->hasParameter($name))
			return $this->container->getParameter($name);

		if (false === strpos($name, '.'))
			return $default;

		$pName = explode('.', $name);

		$key = array_pop($pName);

		$name = implode('.', $pName);

		$value = $this->getParameter($name, $default);

		if (is_array($value) && isset($value[$key]))
			return $value[$key];

		throw new \InvalidArgumentException(sprintf('The value %s is not a valid array parameter.', $name));
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
			$this->container->get('session')->getFlashBag()->add('error', $this->container->get('translator')->trans('updateDatabase.failure', array('%fName%' => $fName), 'System'));

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


	/**
	 * @param $name
	 *
	 * @return mixed
	 */
	private function getSettingArray($name)
	{
		$x = explode('.', $name);
		$key = array_pop($x);
		$name = implode('.', $x);
		$value = $this->setting->getValue();
		foreach($value as $keyName=>$result)
		{
			if (strtolower($keyName) === $key)
				return $result;
		}
		return $this->getSettingArray($name);
	}

	private function loadSettingsFromSession()
	{
		if (! is_null($this->settings))
			return;

		$this->settings = $this->session->get('settings');
		$this->settingCache = $this->session->get('settingCache');
		$this->settingExists = $this->session->get('settingExists');
	}

	/**
	 * @param Request       $request
	 * @param FormInterface $form
	 */
	public function handleImportRequest(Request $request, FormInterface $form)
	{
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			$file = $form->get('import_file')->getData();
			try
			{
				$data = Yaml::parse(file_get_contents($file->getPathName()));
			} catch (\Exception $e) {
				$this->messageManager->add('danger', 'setting.import.import.error', ['%{message}' => $e->getMessage()], 'Setting');
				return ;
			}

			if ($data['name'] !== $form->get('import_file')->getData()->getClientOriginalName())
			{
				$this->messageManager->add('danger', 'setting.import.name.error', ['%{name}' => $data['name']], 'Setting');
				return;
			}

			$this->buildSettings($this->convertSettings($data['settings']), $data['name']);
		}

	}

	/**
	 * @param $content
	 *
	 * @return array
	 */
	private function convertSettings($content): array
	{
		$settings = [];
		foreach ($content as $name => $value)
			$settings[$name]['value'] = $value;

		return $settings;
	}

	/**
	 * @param $data
	 * @param $resource
	 */
	private function buildSettings($data, $resource)
	{
		if (empty($data))
			return;
		foreach ($data as $name => $datum)
		{
			$entity = $this->getSettingEntity($name);

			if (!$entity instanceof Setting)
			{
				$entity = new Setting();
				$entity->setName($name);
				if (empty($datum['type']))
				{
					$this->messageManager->addMessage('warning', 'setting.resource.warning', ['{{name}}' => $name], 'System');
					continue;
				}
				$entity->setType($datum['type']);
			}
			foreach ($datum as $field => $value)
			{
				$w = 'set' . ucwords($field);
				$entity->$w($value);
			}
			$this->createSetting($entity);
		}

		$this->messageManager->addMessage('success', 'setting.resource.success', ['{{name}}' => $resource], 'System');
	}

    /**
     * @param int $id
     * @return Setting|null
     */
    public function find(int $id): ?Setting
    {
        $this->setting = $this->settingRepo->find($id);

        if ($this->setting instanceof Setting)
        {
            $this->settings[$this->setting->getName()] = $this->setting;
            $this->writeSettingToSession($this->setting->getName());
            $this->settingExists[$this->setting->getName()] = true;
        }

        return $this->setting;
    }
}