<?php
namespace App\Core\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Hillrange\Security\Util\ParameterInjector;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2Client;
use KnpU\OAuth2ClientBundle\Exception\MissingAuthorizationCodeException;
use KnpU\OAuth2ClientBundle\Security\Exception\NoAuthCodeAuthenticationException;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AuthenticatorInterface;

class GoogleAuthenticator implements AuthenticatorInterface
{
	/**
	 * @var ClientRegistry
	 */
	private $clientRegistry;

	/**
	 * @var EntityManagerInterface
	 */
	private $em;

	/**
	 * @var RouterInterface
	 */
	private $router;

	/**
	 * @var MessageManager
	 */
	private $messageManager;

	/**
	 * @var ParameterInjector
	 */
	private $parameterInjector;

	/**
	 * @var LoggerInterface
	 */
	private $logger;

	/**
	 * @var Object
	 */
	private $google_user;

	/**
	 * GoogleAuthenticator constructor.
	 *
	 * @param ClientRegistry         $clientRegistry
	 * @param EntityManagerInterface $em
	 * @param RouterInterface        $router
	 */
	public function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $em, RouterInterface $router, MessageManager $messageManager, ParameterInjector $parameterInjector, LoggerInterface $logger)
	{
		$this->clientRegistry = $clientRegistry;
		$this->em = $em;
		$this->router = $router;
		$this->messageManager = $messageManager;
		$this->parameterInjector = $parameterInjector;
		$this->logger = $logger;
	}

	public function getCredentials(Request $request)
	{
		$this->logger->debug("Google Authentication: Google authentication attemped.");

		return $this->fetchAccessToken($this->getGoogleClient());
	}

	public function getUser($credentials, UserProviderInterface $userProvider)
	{
		/** @var GoogleUser $googleUser */
		$this->google_user = $this->getGoogleClient()
			->fetchUserFromToken($credentials);

		// 1) have they logged in with Google before? Easy!
/*		$existingUser = $this->em->getRepository(User::class)
			->findOneBy(['googleId' => $googleUser->getId()]);
		if ($existingUser) {
			return $existingUser;
		}
*/
		// 2) do we have a matching user by email?
		$user = $userProvider->loadUserByUsername($this->google_user->getEmail());

		// 3) Maybe you just want to "register" them by creating
		// a User object
//		$user->setGoogleId($googleUser->getId());
//		$this->em->persist($user);
//		$this->em->flush();

		return $user;
	}

	/**
	 * @return
	 */
	private function getGoogleClient()
	{
		return $this->clientRegistry
			// "google" is the key used in knpu_oauth2_client.yaml
			->getClient('google');
	}

	/**
	 * @param Request                 $request
	 * @param AuthenticationException $exception
	 *
	 * @return null|RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
	{
		$this->logger->notice("Google Authentication: ".  $exception->getMessage());

		return new RedirectResponse($this->router->generate($this->parameterInjector->getParameter('security.routes.security_user_login')));
	}

	public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
	{
		$user = $token->getUser();
		$this->logger->notice("Google Authentication: User #" . $user->getId() . " (" . $user->getEmail() . ") The user authenticated via Google.");

		$user->setUserSetting('google_id', $this->google_user->getId(), 'string');

		$this->em->persist($user);
		$this->em->flush();

		if (null !== $user->getLocale())
			$request->setLocale($user->getLocale());

		return new RedirectResponse($this->router->generate($this->parameterInjector->getParameter('security.routes.security_home')));
	}

	public function start(Request $request, AuthenticationException $authException = null)
	{
		return new RedirectResponse($this->router->generate($this->parameterInjector->getParameter('security.routes.security_user_login')));
	}

	/**
	 * @param UserInterface $user
	 * @param string        $providerKey
	 *
	 * @return UsernamePasswordToken|\Symfony\Component\Security\Guard\Token\GuardTokenInterface
	 */
	public function createAuthenticatedToken(UserInterface $user, $providerKey)
	{
		return new UsernamePasswordToken(
			$user,
			$user->getPassword(),
			$providerKey,
			$user->getRoles()
		);
	}

	/**
	 * @param mixed         $credentials
	 * @param UserInterface $user
	 *
	 * @return bool
	 */
	public function checkCredentials($credentials, UserInterface $user)
	{
		return true;
	}

	/**
	 * @param Request $request
	 *
	 * @return bool
	 */
	public function supports(Request $request): bool
	{
		if ($request->getPathInfo() != '/security/oauth2callback/')
			return false;

		return true;
	}

	/**
	 * @param OAuth2Client $client
	 *
	 * @return \League\OAuth2\Client\Token\AccessToken
	 * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
	 */
	protected function fetchAccessToken(OAuth2Client $client)
	{
		try {
			return $client->getAccessToken();
		} catch (MissingAuthorizationCodeException $e) {
			throw new NoAuthCodeAuthenticationException();
		}
	}

	/**
	 * @return bool
	 */
	public function supportsRememberMe()
	{
		return true;
	}
}