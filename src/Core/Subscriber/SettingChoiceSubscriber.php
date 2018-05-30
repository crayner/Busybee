<?php
namespace App\Core\Subscriber;

use App\Core\Exception\Exception;
use App\Core\Manager\SettingManager;
use App\Core\Type\ChoiceSettingType;
use App\Core\Validator\SettingChoice;
use Hillrange\Form\Type\MessageType;
use PhpParser\Node\Stmt\Else_;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Translation\TranslatorInterface;

class SettingChoiceSubscriber implements EventSubscriberInterface
{
	/**
	 * @var SettingManager
	 */
	private $settingManager;

	/**
	 * @var TranslatorInterface
	 */
	private $translator;

	/**
	 * SettingSubscriber constructor.
	 *
	 * @param   SettingManager $settingManager
	 *
	 * @return  SettingChoiceSubscriber
	 */
	public function __construct(SettingManager $settingManager, TranslatorInterface $translator)
	{
		$this->settingManager = $settingManager;
		$this->translator     = $translator;

		return $this;
	}

	/**
	 * @return array
	 */
	public static function getSubscribedEvents()
	{
		// Tells the dispatcher that you want to listen on the form.pre_set_data
		// event and that the preSetData method should be called.
		return array(
			FormEvents::PRE_SET_DATA  => 'preSetData',
		);
	}

	/**
	 * @param FormEvent $event
     * @throws Exception
	 */
	public function preSetData(FormEvent $event)
	{
		$form = $event->getForm();

		$options = $form->getConfig()->getOptions();
		$name    = $form->getName();

        $newChoices = [];
        $newOptions['label']                = isset($options['label']) ? $options['label'] : false;
        $newOptions['attr']                 = isset($options['attr']) ? $options['attr'] : [];
        $newOptions['help']                 = isset($options['help']) ? $options['help'] : '';
        $newOptions['translation_domain']   = isset($options['translation_domain']) ? $options['translation_domain'] : 'System';
        $newOptions['placeholder']          = isset($options['placeholder']) ? $options['placeholder'] : null;
        $newOptions['required']             = isset($options['required']) ? $options['required'] : false;

        $choices = $this->settingManager->get($options['setting_name']);


        $setting = $this->settingManager->getCurrentSetting();
		if (is_null($setting)) {
            $form->getParent()->add($name, MessageType::class,
                [
                    'label' => isset($options['label']) ? $options['label'] : false,
                    'help' => 'setting.choice.name.not_found',
                    'help_params' => ['%{name}' => $options['setting_name']],
                    'translation_domain' => isset($options['translation_domain']) ? $options['translation_domain'] : $this->getParentTranslationDomain($form),
                ]
            );
            return ;
        }

        $test = $choices;
        ksort($test);
        end($test);
        $sequentialChoices = false;
        if ((count($test) - 1) === intval(key($test)) && $test === $choices)
            $sequentialChoices = true;

		if (!is_null($options['setting_data_value']))
		{
			if (!is_array($choices)){
                $form->getParent()->add($name, MessageType::class,
                    [
                        'help' => 'setting.choice.empty.error',
                        'help_params' => [
                            '%{name}' => $options['setting_name'],
                        ],
                        'translation_domain' => 'System',
                        'label' => false,
                    ]
                );
                return ;
            }

			foreach ($choices as $label => $data)
			{
				if (is_array($data))
				{
					if (!is_null($options['setting_data_name']) && !empty($data[$options['setting_data_name']]))
						$newChoices[$data[$options['setting_data_name']]] = $data[$options['setting_data_value']];
					else {
                        $newChoices[$data[$options['setting_data_value']]] = $data[$options['setting_data_value']];
                    }
				} else {
                    $form->getParent()->add($name, MessageType::class,
                        [
                            'help' => 'setting.choice.sub_array.error',
                            'help_params' => [
                                '%{name}' => $options['setting_name'],
                                '%{data}' => $options['setting_data_value'].'/'.$options['setting_data_name'],
                            ],
                            'translation_domain' => 'System',
                            'label' => false,
                        ]
                    );
                    return ;
				}
			}
		} else {
            $key = '';
            if ($options['translation_prefix'])
                $key = $options['setting_name'];

            $newChoices = self::generateChoices($key, $choices);
        }

		$choices = $newChoices;
dump($choices);

        if (empty($choices)) {
            $form->getParent()->add($name, MessageType::class,
                [
                    'help' => 'setting.choice.empty.error',
                    'help_params' => [
                        '%{name}' => $options['setting_name'],
                    ],
                    'translation_domain' => 'System',
                    'label' => false,
                ]
            );
            return;
        }

        $newOptions                              = [];
        $newOptions['constraints']               = [];
        $newOptions['choices']                   = $choices;
        if ($options['sort_choice'])
            asort($choices);
		$newOptions['multiple']                  = isset($options['multiple']) ? $options['multiple'] : false;
		$newOptions['expanded']                  = isset($options['expanded']) ? $options['expanded'] : false;
		$newOptions['mapped']                    = isset($options['mapped']) ? $options['mapped'] : true;
		$newOptions['choice_translation_domain'] = isset($options['choice_translation_domain']) ? $options['choice_translation_domain'] : $setting->getTranslateChoice() ?: 'Setting';
		if ($options['translation_prefix'] === false)
		    $newOptions['choice_translation_domain'] = false;
        if ($setting->hasChoice())
            $newOptions['constraints'][] = new SettingChoice(['settingName' => $options['setting_name'], 'settingDataValue' => $options['setting_data_value']]);

        $newOptions['data'] = $event->getData() ?: '0';
 		$newOptions['setting_name'] = $options['setting_name'];
		$newOptions['setting_display_name'] = $options['setting_display_name'] ? $options['setting_display_name'] : $setting->getDisplayName();

		$newOptions['constraints'] = array_merge(is_array($options['constraints']) ? $options['constraints'] : [], $newOptions['constraints']);

		//  Now replace the existing setting form element with a straight Choice
		$form->getParent()->add($name, ChoiceSettingType::class, $newOptions);
	}

    /**
     * getParentTranslationDomain
     *
     * @param FormInterface $form
     * @return null|string
     */
    private function getParentTranslationDomain(FormInterface $form): ?string
    {
        $form = $form->getParent();

        if (is_null($form))
            return null;

        $options = $form->getConfig()->getOptions();
        if (! empty($options['translation_domain']))
            return $options['translation_domain'];

        return $this->getParentTranslationDomain($form);
    }


    /**
     * generateTranslationKeys
     *
     * @param $key
     * @param $data
     * @return array
     */
    public static function generateChoices($key, $data)
    {
        $results = [];
        foreach($data as $name => $value)
        {
            if ($name === $value)
                $results[rtrim($key. '.' . $value, '.')] = $value;
            elseif (strval(intval($name)) !== trim($name) && ! is_array($value))
                $results[$name] = ltrim($key. '.' . $value, '.');
            elseif (is_array($value))
                $results[$name] = self::generateChoices($key, $value);
            else
                if (strval(intval($name)) !== trim($name))
                    $results[ltrim($key. '.' . $value, '.')] = $name ;
                else
                    if (! empty($value))
                        $results[rtrim($key. '.' . $value, '.')] = $value;

        }
        return $results;
    }

}