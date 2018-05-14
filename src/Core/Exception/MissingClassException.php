<?php
namespace App\Core\Exception;

class MissingClassException extends \RuntimeException
{
    /**
     * @var array
     */
    private $options;

    /**
     * MissingClassException constructor.
     * @param string $message
     * @param array $options
     */
    public function __construct(string $message, array $options)
    {
        parent::__construct($message);

        $this->options = $options;
    }

    /**
     * getOptions
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}