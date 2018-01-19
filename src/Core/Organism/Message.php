<?php
namespace App\Core\Organism;

class Message
{
	/**
	 * @var string
	 */
	private $level;

	/**
	 * @var string
	 */
	private $message;

	/**
	 * @var array
	 */
	private $options;

	/**
	 * @var string
	 */
	private $domain;

	/**
	 * Message constructor.
	 */
	public function __construct()
	{
		$this->setOptions([]);
	}

	/**
	 * @return string
	 */
	public function getLevel(): string
	{
		return $this->level;
	}

	/**
	 * @param string $level
	 */
	public function setLevel(string $level)
	{
		$this->level = $level;
	}

	/**
	 * @return string
	 */
	public function getMessage(): string
	{
		return $this->message;
	}

	/**
	 * @param string $message
	 */
	public function setMessage(string $message)
	{
		$this->message = $message;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getOptions(): array
	{
		return $this->options;
	}

	/**
	 * @param array $options
	 */
	public function setOptions(array $options)
	{
		$this->options = $options;

		return $this;
	}

	public function addOption($name, $element)
	{
		$this->options[$name] = $element;

		return $this;
	}

	public function removeOption($name)
	{
		if (isset($this->options[$name]))
			unset($this->options[$name]);

		return $this;
	}

	/**
	 * @return string
	 */
	public function getDomain(): string
	{
		if (empty($this->domain))
			return 'home';

		return $this->domain;
	}

	/**
	 * @param string $domain
	 */
	public function setDomain(string $domain)
	{
		$this->domain = $domain;

		return $this;
	}
}