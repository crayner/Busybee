<?php
namespace App\Core\Extension;

use App\Core\Exception\Exception;
use Symfony\Component\Form\FormView;
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
    class: "fas fa-download btn btn-success"
    type: submit
    title: 'form.save'
    style: 'float: right;'
    name: Save
    attr: ''
    prompt: ''
cancel:
    class: "far fa-times-circle btn btn-info"
    type: button
    title: 'form.cancel'
    style: 'float: right;'
    attr: ''
    prompt: ''
upload:
    class: "fas fa-cloud-upload-alt btn btn-success"
    type: submit
    title: 'form.upload'
    style: 'float: right;'
    attr: ''
    prompt: ''
add:
    class: "fas fa-plus-circle btn btn-info"
    type: button
    title: 'form.add'
    style: 'float: right;'
    attr: ''
    prompt: ''
edit:
    class: "fas fa-edit btn btn-info"
    type: button
    title: 'form.edit'
    style: 'float: right;'
    attr: ''
    prompt: ''
proceed:
    class: "far fa-hand-point-right btn btn-info"
    type: button
    title: 'form.proceed'
    style: 'float: right;'
    attr: ''
    prompt: ''
return:
    class: "far fa-hand-point-left btn btn-primary"
    type: button
    title: 'form.return'
    style: 'float: right;'
    attr: ''
    prompt: ''
delete:
    class: "fas fa-eraser btn btn-danger"
    type: button
    title: 'form.delete'
    style: 'float: right;'
    attr: ''
    prompt: ''
remove:
    class: "fas fa-eraser btn btn-warning collection-remove collection-action"
    type: button
    title: 'form.remove'
    style: 'float: right;'
    attr: ''
    prompt: ''
reset:
    class: "fas fa-sync btn btn-warning"
    type: reset
    title: 'form.reset'
    style: 'float: right;'
    name: Reset
    prompt: ''
    attr: ''
misc:
    class: ""
    type: button
    title: 'form.misc'
    style: 'float: right;'
    attr: ''
    prompt: ''
close:
    class: "far fa-times-circle btn btn-primary"
    type: button
    title: 'form.close'
    style: 'float: right;'
    attr: 'onclick="window.close();"'
    prompt: ''
duplicate:
    class: "fas fa-copy btn btn-primary"
    type: button
    title: 'form.duplicate'
    style: 'float: right;'
    attr: ''
    prompt: ''
up:
    class: "collection-up collection-action fas fa-arrow-up btn btn-light"
    type: button
    title: 'form.up'
    style: 'float: right;'
    attr: ''
    prompt: ''
down:
    class: "collection-down collection-action fas fa-arrow-down btn btn-light"
    type: button
    title: 'form.down'
    style: 'float: right;'
    attr: ''
    prompt: ''
on:
    class: "far fa-thumbs-up btn btn-danger"
    type: button
    title: 'form.on'
    style: 'float: right;'
    attr: ''
    prompt: ''
off:
    class: "far fa-thumbs-down btn btn-success"
    type: button
    title: 'form.off'
    style: 'float: right;'
    attr: ''
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
            new \Twig_SimpleFunction('removeButton', array($this, 'removeButton')),
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
	public function saveButton($details = [])
	{
		return $this->generateButton($this->buttons['save'], $details);
	}

	/**
	 * @param array $defaults
	 * @param array $details
	 *
	 * @return string
	 */
	private function generateButton($defaults, $details = []): string
	{
		$button = '<button name="%name%" title="%title%" type="%type%" class="%class%" style="%style%" %attr%>%prompt%</button>';

		if (isset($details['mergeClass']))
		{
			if (isset($defaults['class']))
				$defaults['class'] .= ' ' . $details['mergeClass'];
		}

		if (! empty($details['additional']))
		    $details['attr'] = $details['additional'];

		if (isset($details['attr']) && is_array($details['attr']))
		{
			$attr            = $details['attr'];
			$details['attr'] = '';
			foreach ($attr as $name => $value)
				$details['attr'] .= $name . '="' . $value . '" ';
			$details['attr'] = trim($details['attr']);
		}

		if (!empty($details['windowOpen']))
		{
			$target                = empty($details['windowOpen']['target']) ? '_self' : $this->translator->trans($details['windowOpen']['target'], array(), empty($details['transDomain']) ? 'FormTheme' : $details['transDomain']);
			$route                 = 'onClick="window.open(\'' . $details['windowOpen']['route'] . '\',\'' . $target . '\'';
			$route                 = empty($details['windowOpen']['params']) ? $route . ')"' : $route . ',\'' . $details['windowOpen']['params'] . '\')"';
			$details['attr'] = empty($details['attr']) ? $route : trim($details['attr'] . ' ' . $route);
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
			$details['attr'] = empty($details['attr']) ? $route : trim($details['attr'] . ' ' . $route);
		}

		if (!empty($details['disabled']) && $details['disabled'])
            $details['attr'] = empty($details['attr']) ? 'disabled' : trim($details['attr'] . ' disabled');

		if (!isset($defaults['name']))
            $defaults['name'] = '';

		foreach ($defaults as $q => $w)
		{
            if ($q == 'attr')
                $details['attr'] = empty($details['attr']) ? $w : trim($details['attr'] . ' ' . $w);
			if (isset($details[$q]))
				$defaults[$q] = $details[$q];
            if ($q == 'name')
                $details['attr'] = empty($details['attr']) ? '' : trim($details['attr'] . ' ' . $w);
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
			$button = str_replace(['btn-default', 'btn-success', 'btn-info', 'btn-warning', 'btn-danger', 'btn-primary', 'btn-link', 'btn-light', 'btn-dark'], 'btn-' . $details['colour'], $button);

		return $button;
	}

	/**
	 * @param array $details
	 *
	 * @return string
	 */
	public function cancelButton($details = [])
	{
		return $this->generateButton($this->buttons['cancel'], $details);
	}

	/**
	 * @param array $details
	 *
	 * @return string
	 */
	public function uploadButton($details = [])
	{
		return $this->generateButton($this->buttons['upload'], $details);
	}

	/**
	 * @param array $details
	 *
	 * @return string
	 */
	public function addButton($details = [])
	{
		return $this->generateButton($this->buttons['add'], $details);
	}

	/**
	 * @param array $details
	 *
	 * @return string
	 */
	public function editButton($details = [])
	{
		return $this->generateButton($this->buttons['edit'], $details);
	}

	/**
	 * @param array $details
	 *
	 * @return string
	 */
	public function proceedButton($details = [])
	{
		return $this->generateButton($this->buttons['proceed'], $details);
	}

	/**
	 * @param array $details
	 *
	 * @return string
	 */
	public function returnButton($details = [])
	{
	    if (! empty($details['returnTo']))
        {
            $details['windowOpen'] = ['route' => $details['returnTo']];
            unset($details['returnTo']);
        }
		return $this->generateButton($this->buttons['return'], $details);
	}

    /**
     * @param array $details
     *
     * @return string
     */
    public function deleteButton($details = [])
    {
        return $this->generateButton($this->buttons['delete'], $details);
    }

    /**
     * @param array $details
     *
     * @return string
     */
    public function removeButton($details = [])
    {
        return $this->generateCollectionButton($this->buttons['remove'], $details);
    }

	/**
	 * @param array $details
	 *
	 * @return string
	 */
	public function miscButton($details = [])
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
	    $details['mergeClass'] = ! empty($details['mergeClass']) ? $details['mergeClass'] . ' resetButton' : 'resetButton';
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
		return $this->generateCollectionButton($this->buttons['up'], $details);
	}

	/**
	 * @param array $details
	 *
	 * @return string
	 */
	public function downButton($details = array())
	{
		return $this->generateCollectionButton($this->buttons['down'], $details);
	}

	/**
	 * @param array $details
	 *
	 * @return string
	 */
	public function upDownButton(array $details = [])
	{
		return $this->generateCollectionButton($this->buttons['down'], $details) . $this->generateCollectionButton($this->buttons['up'], $details);
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

		$attributes['data-off'] = empty($vars['attr']['data-off']) ? '<span class=\'far fa-thumbs-down\'></span>' : $vars['attr']['data-off'];

		$attributes['data-on'] = empty($vars['attr']['data-on']) ? '<span class=\'far fa-thumbs-up\'></span>' : $vars['attr']['data-on'];

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
		if (!isset($details['value']) && ! is_bool($details['value']))
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
	public function duplicateButton($details = [])
	{
		return $this->generateCollectionButton($this->buttons['duplicate'], $details);
	}

    /**
     * @param $defaults
     * @param $details
     * @return string
     */
    private function generateCollectionButton($defaults, $details): string
    {
        if (! empty($details) && ! empty($details['collection']))
        {
            if ($details['collection'] instanceof FormView)
            {
                $name = $details['collection']->vars['id'];
                $defaults['class'] = str_replace('collection', $name.'-collection', $defaults['class'] ?: '');
            }
            if (is_string($details['collection']))
            {
                $name = $details['collection'];
                $defaults['class'] = str_replace('collection', $name.'-collection', $defaults['class'] ?: '');
            }
            unset($details['collection']);
        }

        return $this->generateButton($defaults, $details);
    }
}