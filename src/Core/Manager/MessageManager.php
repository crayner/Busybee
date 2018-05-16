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
     * @var array
     */
    static $statusLevel = [
        'default'   => 0,
        'light'     => 1,
        'dark'      => 2,
        'secondary' => 4,
        'primary'   => 8,
        'info'      => 16,
        'success'   => 32,
        'warning'   => 64,
        'danger'    => 128,
    ];

	/**
	 * Add Message
	 *
	 * @param string      $level
	 * @param string      $message
	 * @param array       $options
     * Special Options
     *     useRaw: The twig template will return raw content of the message.
     *     transChoice: The twig template use transChoice (and the transChoice value.)
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
		return $this->addMessage($level, $message, $options, $domain);
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
	    if (empty($this->messages))
	        $this->messages = [];
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
     * @return bool
     */
    public function hasMessages(): bool
    {
        if (count($this->getMessages()) > 0)
            return true;
        return false;
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

    /**
     * Compare Level
     *
     * Returns true if stat1 is higher that stat2, or if stat1 >= stat2 when compare set to '>=" otherwise false.
     * @param string $stat1
     * @param string $stat2
     * @param string $compare
     * @return bool
     */
    public static function compareLevel($stat1, $stat2 = 'default', string $compare = '>'): bool
    {
        $stat1 = strtolower($stat1);
        $stat2 = strtolower($stat2);
        if (! in_array($stat1, self::$statusLevel))
            return false;
        if (! in_array($stat2, self::$statusLevel))
            return false;

        if ($compare === '>' && self::$statusLevel[$stat1] > self::$statusLevel[$stat2])
            return true;
        if ($compare === '>=' && self::$statusLevel[$stat1] >= self::$statusLevel[$stat2])
            return true;
        return false;
    }

    /**
     * @return string
     */
    public function getHighestLevel(): string
    {
        $x =  'default';
        foreach($this->getMessages() as $message) {
            if (self::compareLevel($message->getLevel(), $x))
                $x = $message->getLevel();
            if ($x === 'danger')
                break;
        }
        return $x;
    }

    /**
     * addStatusMessages
     *
     * @param $status
     * @param null $domain
     * @param string $level
     * @return MessageManager
     */
    public function addStatusMessages($status, $domain = null, string $level = 'default'): MessageManager
    {
        if (empty($status))
            return $this;

        if (! is_array($status))
            $status = [$status];

        foreach($status as $item) {
            if (self::compareLevel($item->level, $level, '>=')) {
                if (is_array($item))
                    $this->add($item['level'], $item['message'], $item['options'], $domain);
                elseif ($item instanceof \stdClass)
                    $this->add($item->level, $item->message, $item->options, $domain);
            }
        }
        return $this;
    }
}