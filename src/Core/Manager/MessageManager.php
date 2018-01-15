<?php
namespace App\Core\Manager;

use App\Core\Organism\Message;

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
	public function __construct(string $domain = 'Busybee')
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
}