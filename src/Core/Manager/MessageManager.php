<?php
namespace App\Core\Manager;

use App\Core\Organism\Message;
use Symfony\Component\HttpFoundation\Session\Session;

class MessageManager
{
	/**
	 * @var string
	 */
	private $domain = 'home';

	/**
	 * @var array
	 */
	private $messages = [];

	/**
	 * Add Message
	 *
	 * @param string      $level
	 * @param string      $message
	 * @param array       $options
	 * @param string|null $domain
	 *
	 * @return $this
	 */
	public function addMessage(string $level, string $message, array $options = [], string $domain = null)
	{
		$mess = new Message();

		$mess->setDomain(is_null($domain) ? $this->getDomain() : $domain);
		$mess->setLevel($level);
		$mess->setMessage($message);
		foreach ($options as $name => $element)
			$mess->addOption($name, $element);

		$this->messages[] = $mess;

		return $this;
	}

	/**
	 * Add Message (Synonym)
	 *
	 * @param string      $level
	 * @param string      $message
	 * @param array       $options
	 * @param string|null $domain
	 *
	 * @return $this
	 */
	public function add(string $level, string $message, array $options = [], string $domain = null)
	{
		return $this->addMessage($level, $message,$options,  $domain );
	}

	/**
	 * @return string
	 */
	public function getDomain(): string
	{
		return $this->domain;
	}

	/**
	 * @param string $domain
	 */
	public function setDomain(string $domain): MessageManager
	{
		$this->domain = $domain;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getMessages(): array
	{
		return $this->messages;
	}

	/**
	 * @return MessageManager
	 */
	public function clearMessages(): MessageManager
	{
		$this->messages = [];

		return $this;
	}

	/**
	 * MessageManager constructor.
	 *
	 * @param string|null $domain
	 */
	public function __construct(string $domain = 'home')
	{
		$this->setDomain($domain);
	}

	/**
	 * @return int
	 */
	public function count(): int
	{
		return count($this->messages);
	}

    /**
     * @param \Twig_Environment|null $twig
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function renderView(\Twig_Environment $twig = null)
	{
		if (! $twig instanceof \Twig_Environment)
			throw new \LogicException('You can not use the "render" method if the Templating Component or the Twig Bundle are not available. Try running "composer require symfony/twig-bundle".');

		return $twig->render('Default/messages.html.twig', ['messages' => $this]);
	}

	public function addToFlash(Session $session)
    {

    }
}