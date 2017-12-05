<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

abstract class BusybeeController extends Controller
{
	/**
	 * Throws an exception unless the attributes are granted against the current authentication token and optionally
	 * supplied object.
	 *
	 * @param mixed  $attributes The attributes
	 * @param mixed  $subject     The object
	 * @param string $message    The message passed to the exception
	 *
	 * @throws AccessDeniedException
	 */
	protected function denyAccessUnlessGranted($attributes, $subject = null, string $message = 'Access Denied')
	{
		$request   = $this->get('request_stack')->getCurrentRequest();
		$routeName = $request->get('_route');

		$page = $this->get('busybee_core_security.model.page_manager')->findOneByRoute($routeName, $attributes);

		$dev = $this->get('kernel')->getEnvironment();

		if ($dev === 'dev' && !is_string($this->get('busybee_core_security.model.get_current_user')))
			$message = is_null($message) ? $this->get('translator')->trans('security.access.denied.dev', ['%name%' => $routeName, '%page%' => implode(', ', $page->getRoles()), '%user%' => $this->get('busybee_core_security.model.get_current_user')->rolesToString()], 'BusybeeSecurityBundle') : $message;
		else
			$message = is_null($message) ? $this->get('translator')->trans('security.access.denied.prod', ['%name%' => $routeName], 'BusybeeSecurityBundle') : $message;


		/*		if (! $this->isGranted($page->getRoles(), $subject))
				{
					$mm = new MessageManager('SystemBundle');
					$mm->addMessage('danger', $message);

					$this->get('busybee_core_system.model.flash_bag_manager')->addMessages($mm);

				} */
		parent::denyAccessUnlessGranted($page->getRoles(), $subject, $message);
	}
}