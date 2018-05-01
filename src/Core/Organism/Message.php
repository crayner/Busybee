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
     * @var mixed
     */
	private $transChoice = false;

    /**
     * @var bool
     */
	private $useRaw = false;

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
		return strtolower($this->message);
	}

	/**
	 * @param string $message
	 */
	public function setMessage(string $message)
	{
		$this->message = strtolower($message);

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

    /**
     * @param $name
     * Special Options
     *     useRaw: The twig template will return raw content of the message.
     *     transChoice: The twig template use transChoice (and the transChoice value.)
     * @param $element
     * @return $this|Message
     */
    public function addOption($name, $element)
	{
        if ($name === 'transChoice')
            return $this->setTransChoice($element);

        if ($name === 'useRaw')
            return $this->setUseRaw();

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

    /**
     * @return mixed
     */
    public function getTransChoice()
    {
        return $this->transChoice;
    }

    /**
     * @param mixed $transChoice
     * @return Message
     */
    public function setTransChoice($transChoice)
    {
        $this->transChoice = $transChoice;
        return $this;
    }

    /**
     * @return bool
     */
    public function isUseRaw(): bool
    {
        return $this->useRaw;
    }

    /**
     * @return Message
     */
    public function setUseRaw(): Message
    {
        $this->useRaw = true;
        return $this;
    }
}