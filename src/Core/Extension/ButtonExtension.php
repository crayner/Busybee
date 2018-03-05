<?php
namespace App\Core\Extension;

use App\Core\Exception\Exception;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Yaml\Yaml;
use Twig\Extension\AbstractExtension;

class ButtonExtension extends AbstractExtension
{
	/**
	 * @var array
	 */
	private $buttons;

	/**
	 * @var TranslatorInterface
	 */
	private $translator;

	/**
	 * ButtonExtension constructor.
	 *
	 * @param TranslatorInterface $translator
	 */
	public function __construct(TranslatorInterface $translator)
	{
		$this->translator = $translator;
		$buttons = <<<XXX
save:
    class: "halflings halflings-save btn btn-success"
    type: submit
    title: 'form.save'
    style: 'float: right;'
    additional: ''
    prompt: ''
cancel:
    class: "halflings halflings-remove-circle btn btn-info"
    type: button
    title: 'form.cancel'
    style: 'float: right;'
    additional: ''
    prompt: ''
upload:
    class: "halflings halflings-cloud-upload btn btn-success"
    type: submit
    title: 'form.upload'
    style: 'float: right;'
    additional: ''
    prompt: ''
add:
    class: "halflings halflings-plus-sign btn btn-info"
    type: button
    title: 'form.add'
    style: 'float: right;'
    additional: ''
    prompt: ''
edit:
    class: "halflings halflings-edit btn btn-info"
    type: button
    title: 'form.edit'
    style: 'float: right;'
    additional: ''
    prompt: ''
proceed:
    class: "halflings halflings-hand-right btn btn-info"
    type: button
    title: 'form.proceed'
    style: 'float: right;'
    additional: ''
    prompt: ''
return:
    class: "halflings halflings-hand-left btn btn-primary"
    type: button
    title: 'form.return'
    style: 'float: right;'
    additional: ''
    prompt: ''
delete:
    class: "halflings halflings-erase btn btn-danger"
    type: button
    title: 'form.delete'
    style: 'float: right;'
    additional: ''
    prompt: ''
reset:
    class: "halflings halflings-refresh btn btn-warning"
    type: reset
    title: 'form.reset'
    style: 'float: right;'
    additional: ''
    prompt: ''
misc:
    class: ""
    type: button
    title: 'form.misc'
    style: 'float: right;'
    additional: ''
    prompt: ''
close:
    class: "halflings halflings-remove-sign btn btn-primary"
    type: button
    title: 'form.close'
    style: 'float: right;'
    additional: 'onclick="window.close();"'
    prompt: ''
duplicate:
    class: "halflings halflings-duplicate btn btn-primary"
    type: button
    title: 'form.duplicate'
    style: 'float: right;'
    additional: ''
    prompt: ''
up:
    class: "collection-up collection-action halflings halflings-arrow-up btn btn-light"
    type: button
    title: 'form.up'
    style: 'float: right;'
    additional: ''
    prompt: ''
down:
    class: "collection-down collection-action halflings halflings-arrow-down btn btn-light"
    type: button
    title: 'form.down'
    style: 'float: right;'
    additional: ''
    prompt: ''
on:
    class: "halflings halflings-thumbs-up btn btn-danger"
    type: button
    title: 'form.on'
    style: 'float: right;'
    additional: ''
    prompt: ''
off:
    class: "halflings halflings-thumbs-down btn btn-success"
    type: button
    title: 'form.off'
    style: 'float: right;'
    additional: ''
    prompt: ''
XXX;
		$this->buttons = Yaml::parse($buttons);
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'button_twig_extension';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFunctions()
	{
		return array(
			new \Twig_SimpleFunction('saveButton', array($this, 'saveButton')),
			new \Twig_SimpleFunction('cancelButton', array($this, 'cancelButton')),
			new \Twig_SimpleFunction('uploadButton', array($this, 'uploadButton')),
			new \Twig_SimpleFunction('addButton', array($this, 'addButton')),
			new \Twig_SimpleFunction('editButton', array($this, 'editButton')),
			new \Twig_SimpleFunction('proceedButton', array($this, 'proceedButton')),
			new \Twig_SimpleFunction('returnButton', array($this, 'returnButton')),
			new \Twig_SimpleFunction('deleteButton', array($this, 'deleteButton')),
			new \Twig_SimpleFunction('miscButton', array($this, 'miscButton')),
			new \Twig_SimpleFunction('resetButton', array($this, 'resetButton')),
			new \Twig_SimpleFunction('closeButton', array($this, 'closeButton')),
			new \Twig_SimpleFunction('upButton', array($this, 'upButton')),
			new \Twig_SimpleFunction('downButton', array($this, 'downButton')),
			new \Twig_SimpleFunction('onButton', array($this, 'onButton')),
			new \Twig_SimpleFunction('offButton', array($this, 'offButton')),
			new \Twig_SimpleFunction('onOffButton', array($this, 'onOffButton')),
			new \Twig_SimpleFunction('upDownButton', array($this, 'upDownButton')),
			new \Twig_SimpleFunction('toggleButton', array($this, 'toggleButton')),
			new \Twig_SimpleFunction('duplicateButton', array($this, 'duplicateButton')),
		);
	}

	/**
	 * @param array $details
	 *
	 * @return string
	 */
	public function saveButton($details = array())
	{
		return $this->generateButton($this->buttons['save'], $details);
	}

	/**
	 * @param array $defaults
	 * @param array $details
	 *
	 * @return mixed|string
	 */
	private function generateButton($defaults, $details = array())
	{
		$button = '<button title="%title%" type="%type%" class="%class%" style="%style%" %additional%>%prompt%</button>';

		if (isset($details['mergeClass']))
		{
			if (isset($defaults['class']))
				$defaults['class'] .= ' ' . $details['mergeClass'];
		}

		if (isset($details['additional']) && is_array($details['additional']))
		{
			$additional            = $details['additional'];
			$details['additional'] = '';
			foreach ($additional as $name => $value)
				$details['additional'] .= $name . '="' . $value . '" ';
			$details['additional'] = trim($details['additional']);
		}

		if (!empty($details['windowOpen']))
		{
			$target                = empty($details['windowOpen']['target']) ? '_self' : $this->translator->trans($details['windowOpen']['target'], array(), empty($details['transDomain']) ? 'FormTheme' : $details['transDomain']);
			$route                 = 'onClick="window.open(\'' . $details['windowOpen']['route'] . '\',\'' . $target . '\'';
			$route                 = empty($details['windowOpen']['params']) ? $route . ')"' : $route . ',\'' . $details['windowOpen']['params'] . '\')"';
			$details['additional'] = empty($details['additional']) ? $route : trim($details['additional'] . ' ' . $route);
		}

		if (!empty($details['javascript']))
		{
			$target = '';
			if (!empty($details['javascript']['options']))
			{
				foreach ($details['javascript']['options'] as $option)
					$target .= '\'' . $option . '\',';
			}
			$target = trim($target, ',');

			$route                 = 'onClick="' . $details['javascript']['function'] . '(' . $target . ');"';
			$details['additional'] = empty($details['additional']) ? $route : trim($details['additional'] . ' ' . $route);
		}

		foreach ($defaults as $q => $w)
		{
			if (isset($details[$q]))
				$defaults[$q] = $details[$q];
			if (empty($defaults[$q]))
			{
				unset($defaults[$q]);
				$button = str_replace(array($q . '="%' . $q . '%"', '%' . $q . '%'), '', $button);
			}
			else
			{
				if (in_array($q, ['title', 'prompt']))
					if (is_array($defaults[$q]))
						$defaults[$q] = $this->translator->trans($defaults[$q]['message'], $defaults[$q]['params'], empty($details['transDomain']) ? 'messages' : $details['transDomain']);
					else
						$defaults[$q] = $this->translator->trans($defaults[$q], [], empty($details['transDomain']) ? 'FormTheme' : $details['transDomain']);
				$button = str_replace('%' . $q . '%', $defaults[$q], $button);
			}
		}

		if (isset($details['collectionName']))
			$button = str_replace('collection', $details['collectionName'], $button);

		if (isset($details['colour']))
			$button = str_replace(['btn-default', 'btn-success', 'btn-info', 'btn-warning', 'btn-danger', 'btn-primary', 'btn-link'], 'btn-' . $details['colour'], $button);

		return $button;
	}

	/**
	 * @param array $details
	 *
	 * @return string
	 */
	public function cancelButton($details = array())
	{
		return $this->generateButton($this->buttons['cancel'], $details);
	}

	/**
	 * @param array $details
	 *
	 * @return string
	 */
	public function uploadButton($details = array())
	{
		return $this->generateButton($this->buttons['upload'], $details);
	}

	/**
	 * @param array $details
	 *
	 * @return string
	 */
	public function addButton($details = array())
	{
		return $this->generateButton($this->buttons['add'], $details);
	}

	/**
	 * @param array $details
	 *
	 * @return string
	 */
	public function editButton($details = array())
	{
		return $this->generateButton($this->buttons['edit'], $details);
	}

	/**
	 * @param array $details
	 *
	 * @return string
	 */
	public function proceedButton($details = array())
	{
		return $this->generateButton($this->buttons['proceed'], $details);
	}

	/**
	 * @param array $details
	 *
	 * @return string
	 */
	public function returnButton($details = array())
	{
		return $this->generateButton($this->buttons['return'], $details);
	}

	/**
	 * @param array $details
	 *
	 * @return string
	 */
	public function deleteButton($details = array())
	{
		return $this->generateButton($this->buttons['delete'], $details);
	}

	/**
	 * @param array $details
	 *
	 * @return string
	 */
	public function miscButton($details = array())
	{
		return $this->generateButton($this->buttons['misc'], $details);
	}

	/**
	 * @param array $details
	 *
	 * @return string
	 */
	public function resetButton($details = array())
	{
		return $this->generateButton($this->buttons['reset'], $details);
	}

	/**
	 * @param array $details
	 *
	 * @return string
	 */
	public function closeButton($details = array())
	{
		return $this->generateButton($this->buttons['close'], $details);
	}

	/**
	 * @param array $details
	 *
	 * @return string
	 */
	public function upButton($details = array())
	{
		return $this->generateButton($this->buttons['up'], $details);
	}

	/**
	 * @param array $details
	 *
	 * @return string
	 */
	public function downButton($details = array())
	{
		return $this->generateButton($this->buttons['down'], $details);
	}

	/**
	 * @param array $details
	 *
	 * @return string
	 */
	public function upDownButton($details = array())
	{
		return $this->generateButton($this->buttons['down'], $details) . $this->generateButton($this->buttons['up'], $details);
	}

	/**
	 * @param array $details
	 *
	 * @return string
	 */
	public function toggleButton(array $details)
	{
		$toggle = '<div class="divClass"><input type="checkbox" attributes inputClass></div>';

		$details['class'] = empty($details['class']) ? 'toggle form-control' : $details['class'] . ' toggle form-control';
		$vars             = $details['form']->vars;

		$toggle = str_replace('divClass', $vars['div_class'], $toggle);

		$attributes = [];

		$attributes['data-toggle'] = 'toggle';

		$attributes['data-off'] = empty($vars['attr']['data-off']) ? '<span class=\'halflings halflings-thumbs-down\'></span>' : $vars['attr']['data-off'];

		$attributes['data-on'] = empty($vars['attr']['data-on']) ? '<span class=\'halflings halflings-thumbs-up\'></span>' : $vars['attr']['data-on'];

		$attributes['data-size'] = empty($vars['attr']['data-size']) ? 'small' : $vars['attr']['data-size'];

		$attributes['data-onstyle'] = empty($vars['attr']['data-onstyle']) ? 'success' : $vars['attr']['data-onstyle'];

		$attributes['data-offstyle'] = empty($vars['attr']['data-offstyle']) ? 'danger' : $vars['attr']['data-offstyle'];

		$attributes['data-height'] = empty($vars['attr']['data-height']) ? '' : $vars['attr']['data-height'];

		$attributes['data-width'] = empty($vars['attr']['data-width']) ? '' : $vars['attr']['data-width'];

		$attributes['name'] = $vars['full_name'];

		$attributes['id'] = $vars['id'];

		if (isset($attributes['value']))
			$attributes['value'] = $vars['value'];

		if ($vars['checked'])
			$attributes['checked'] = 'checked';

		$attributes['style'] = empty($vars['attr']['style']) ? 'float: right;' : $vars['attr']['style'];

		$attrib = '';
		foreach ($attributes as $name => $value)
		{
			$attrib .= ' ' . $name . '="' . $value . '"';
			$attrib = trim($attrib);
		}

		$vars['attr']['class'] = empty($vars['attr']['class']) ? '' : 'class="' . $vars['attr']['class'] . '"';
		$toggle                = str_replace('attributes', $attrib . ' data-height=20 data-width=40', $toggle);
		$toggle                = str_replace('inputClass', $vars['attr']['class'], $toggle);

		return $toggle;
	}

	/**
	 * @param array $details
	 *
	 * @return string
	 */
	public function onButton($details = [])
	{
		return $this->generateButton($this->buttons['on'], $details);
	}

	/**
	 * @param array $details
	 *
	 * @return string
	 */
	public function offButton($details = [])
	{
		return $this->generateButton($this->buttons['off'], $details);
	}

	/**
	 * @param array $details
	 *
	 * @return string
	 */
	public function onOffButton($details = [])
	{
		if (!isset($details['value']))
			throw new Exception('You must set a boolean value for the On/Off Button.  value = ?');
		$details['on'] = isset($details['on']) ? $details['on'] : [];
		$details['off'] = isset($details['off']) ? $details['off'] : [];
		if (isset($details['title']))
		{
			$details['on']['title'] = isset($details['on']['title'])? $details['on']['title'] : $details['title'].'.on';
			$details['off']['title'] = isset($details['off']['title'])? $details['off']['title'] : $details['title'].'.off';
		}

		if (isset($details['transDomain']))
		{
			$details['on']['transDomain'] = isset($details['on']['transDomain']) ? $details['on']['transDomain'] : $details['transDomain'];
			$details['off']['transDomain'] = isset($details['off']['transDomain']) ? $details['off']['transDomain'] : $details['transDomain'];
		}

		if (isset($details['style']))
		{
			$details['on']['style'] = isset($details['on']['style']) ? $details['on']['style'] : $details['style'];
			$details['off']['style'] = isset($details['off']['style']) ? $details['off']['style'] : $details['style'];
		}

		if ($details['value'])
			return $this->generateButton($this->buttons['on'], $details['on']);
		else
			return $this->generateButton($this->buttons['off'], $details['off']);
	}

	/**
	 * @param array $details
	 *
	 * @return string
	 */
	public function duplicateButton($details = array())
	{
		return $this->generateButton($this->buttons['duplicate'], $details);
	}
}