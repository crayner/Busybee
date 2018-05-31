<?php
namespace App\Core\Subscriber;

use App\Core\Exception\Exception;
use App\Core\Manager\SettingManager;
use App\Core\Type\ChoiceSettingType;
use App\Core\Util\SettingChoiceGenerator;
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
     * preSetData
     *
     * @param FormEvent $event
     * @throws \Doctrine\DBAL\Exception\TableNotFoundException
     * @throws \Throwable
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Syntax
     */
    public function preSetData(FormEvent $event)
	{
		$form = $event->getForm();

		$options = $form->getConfig()->getOptions();
		$name    = $form->getName();

        $newOptions = [];
        $newOptions['label']                = isset($options['label']) ? $options['label'] : false;
        $newOptions['attr']                 = isset($options['attr']) ? $options['attr'] : [];
        $newOptions['help']                 = isset($options['help']) ? $options['help'] : '';
        $newOptions['translation_domain']   = isset($options['translation_domain']) ? $options['translation_domain'] : $this->getParentTranslationDomain($form);
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
                    'translation_domain' => 'System',
                ]
            );
            return ;
        }

        $choices = SettingChoiceGenerator::generateChoices($options['translation_prefix'] ? $options['setting_name'] : '', $choices, $options['setting_data_name']);

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

        $newOptions['constraints']               = [];
        if ($options['sort_choice'])
            ksort($choices, SORT_NATURAL);
        $newOptions['choices']                   = $choices;
		$newOptions['multiple']                  = isset($options['multiple']) ? $options['multiple'] : false;
		$newOptions['expanded']                  = isset($options['expanded']) ? $options['expanded'] : false;
		$newOptions['mapped']                    = isset($options['mapped']) ? $options['mapped'] : true;
		$newOptions['choice_translation_domain'] = isset($options['choice_translation_domain']) ? $options['choice_translation_domain'] : $setting->getTranslateChoice() ?: 'Setting';
		if ($options['translation_prefix'] === false)
		    $newOptions['choice_translation_domain'] = false;

		if ($options['validation_off'])
            $newOptions['constraints'][] = new SettingChoice(['settingName' => $options['setting_name'], 'translation' => $options['validation_translation'],
                'settingDataName' => $options['setting_data_name'], 'useLowerCase' => $options['use_lower_case'],
                'strict' => $options['strict_validation'], 'extra_choices' => $options['extra_choices']]);

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
}