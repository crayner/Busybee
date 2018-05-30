<?php
namespace App\Core\Manager;

use App\Core\Organism\Message;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Translation\TranslatorInterface;

class FlashBagManager
{
	/**
	 * @var FlashBagInterface
	 */
	private $flashBag;

	/**
	 * @var TranslatorInterface
	 */
	private $translator;

	/**
	 * @var MessageManager
	 */
	private $messageManager;

	/**
	 * FlashBagManager constructor.
	 *
	 * @param FlashBagInterface   $flashBag
	 * @param TranslatorInterface $translator
	 */
	public function __construct(FlashBagInterface $flashBag, TranslatorInterface $translator, MessageManager $messageManager)
	{
		$this->translator = $translator;
		$this->flashBag   = $flashBag;
		$this->messageManager = $messageManager;
	}

	/**
	 * @param null|array $messages
	 */
	public function addMessages(MessageManager $messages = null)
	{
		$messages = $messages ? $messages : $this->messageManager ;

		foreach ($messages->getMessages() as $message)
		{
			if (!$message instanceof Message)
				continue;
			$this->flashBag->add($message->getLevel(), $this->translator->trans($message->getMessage(), $message->getOptions(), $message->getDomain()));
		}
		$messages->clearMessages();
	}

	/**
	 * @param MessageManager $manager
	 *
	 * @return string
	 */
	public function renderMessages(MessageManager $manager)
	{
		$messages = '';
		foreach ($manager->getMessages() as $message)
		{
			if (!$message instanceof Message)
				continue;
			if ($message->getDomain() === false)
                $messages .= "<div class='alert-dismissible fade show alert alert-" . $message->getLevel() . "'>" . $message->getMessage() . "</div>\n";
			else
			    $messages .= "<div class='alert-dismissible fade show alert alert-" . $message->getLevel() . "'>" . $this->translator->trans($message->getMessage(), $message->getOptions(), $message->getDomain()) . "</div>\n";
		}

		$manager->clearMessages();

		return $messages;
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
		return $this->messageManager->addMessage($level, $message,$options,  $domain );
	}


	/**
	 * @param string $domain
	 */
	public function setDomain(string $domain): FlashBagManager
	{
		$this->messageManager->setDomain($domain);

		return $this;
	}

}