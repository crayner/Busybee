<?php
namespace App\Core\Manager;

use Symfony\Component\Form\FormInterface;

/**
 * This software is provided as-is on the terms of MIT License.
 * Feel free to use, modify and share in ground of both non-commercial and commercial projects.
 *
 * What it does?
 * This service travels through all levels of form and gathers errors from every element.
 * Then it returns errors for you as array of errors - form errors, subforms errors and fields errors.
 * Notice that you need this approach to get ALL errors from form - also form (not fields) errors itself,
 * as card as errors from custom validators you have created.
 *
 *
 * Returned data is array of Symfony\Component\Form\FormError objects (default) or localized messages (use setReturnAsString() method)
 * It can be flat (one level) array (easier to iterate) as card as multidimensional array
 * that represents the structure of your form (easier to join form elements with errors).
 *
 * Why do I need this?
 * I wrote this service to be able to quickly get errors from any form and display them in one place.
 * Now it is my pleasure to share my code with you.
 *
 * @author Maciej Szkamruk <ex3v@ex3v.com>
 */
class FormErrorsParser
{
	/**
	 * This is the main method of service. Pass form object and call it to get resulting array.
	 *
	 * @param   FormInterface $form
	 *
	 * @return  array
	 */
	public function parseErrors(FormInterface $form)
	{
		$results = array();

		return $this->realParseErrors($form, $results);
	}

	/**
	 * This does the actual job. Method travels through all levels of form recursively and gathers errors.
	 *
	 * @param   FormInterface $form
	 * @param   array         &$results
	 *
	 * @return  array
	 */
	private function realParseErrors(FormInterface $form, array &$results)
	{
		$errors = $form->getErrors();

		if (count($errors) > 0)
		{
			$config      = $form->getConfig();
			$name        = $form->getName();
			$label       = $config->getOption('label');
			$translation = $this->getTranslationDomain($form);
			if (empty($label))
			{
				$label = ucfirst(trim(strtolower(preg_replace(array('/([A-Z])/', '/[_\s]+/'), array('_$1', ' '), $name))));
			}

			$results[] = array(
				'name'        => $name,
				'label'       => $label,
				'errors'      => $errors,
				'translation' => $translation,
			);
		}

		$children = $form->all();
		if (count($children) > 0)
		{
			foreach ($children as $child)
			{
				if ($child instanceof FormInterface)
				{
					$this->realParseErrors($child, $results);
				}
			}
		}

		return $results;
	}

	/**
	 * Find the Translation Domain.
	 *
	 * Needs to be done for each element as sub forms or elements could have different translation domains.
	 *
	 * @param   FormInterface $form
	 *
	 * @return  string
	 */
	private function getTranslationDomain(FormInterface $form)
	{
		$translation = $form->getConfig()->getOption('translation_domain');
		if (empty($translation))
		{
			$parent = $form->getParent();
			while (empty($translation))
			{
				$translation = $parent->getConfig()->getOption('translation_domain');
				$parent      = $parent->getParent();
				if (empty($parent) && empty($translation))
					$tranlsation = 'messages';
			}
		}

		return $translation = $translation === 'messages' ? null : $translation;  // Allow the Symfony Default setting to be used by returning null.
	}
}