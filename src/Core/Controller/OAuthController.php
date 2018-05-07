<?php
namespace App\Core\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class OAuthController extends Controller
{
	/**
	 * Link to this controller to start the "connect" process
	 *
	 * @Route("/connect/google/", name="google_oauth")
	 */
	public function connectGoogle()
	{
		// will redirect to Google!
		return $this->get('oauth2.registry')
			->getClient('google') // key used in config.yml
			->redirect();
	}

	/**
	 * After going to Google, you're redirected back here
	 * because this is the "redirect_route" you configured
	 * in config.yml
	 *
	 * @Route("/security/oauth2callback/", name="connect_google_check")
	 */
	public function connectCheckGoogle(Request $request)
	{
	}
}
