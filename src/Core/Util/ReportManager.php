<?php
namespace App\Core\Util;


use App\Core\Manager\MessageManager;

abstract class ReportManager
{
    /**
     * @var string
     */
    private $status = 'default';

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return ReportManager
     */
    public function setStatus(string $status): ReportManager
    {
        if (! in_array($status, MessageManager::$statusLevel) || $this->status === 'danger')
            return $this;
        if (MessageManager::compareLevel($status, $this->status))
            $this->status = $status;
        return $this;
    }

    /**
     * @var array
     */
    private $messages;

    /**
     * @return array
     */
    public function getMessages(): array
    {
        if (empty($this->messages))
            $this->messages = [];
        return $this->messages;
    }

    public function clearMessages(): ReportManager
    {
        $this->messages = [];
        return $this;
    }

    /**
     * @param string $level
     * @param string $message
     * @param array $options
     * @return ReportManager
     */
    public function addMessage(string $level, string $message, array $options = []): ReportManager
    {
        $mess = new \stdClass();
        $mess->level = $level;
        $mess->message = $message;
        $mess->options = $options;
        $this->messages = $this->setStatus($level)
            ->getMessages();
        $this->messages[] = $mess;
        return $this;
    }
}