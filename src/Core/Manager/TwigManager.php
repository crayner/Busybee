<?php
namespace App\Core\Manager;

use App\Core\Exception\MissingClassException;
use App\Core\Organism\Message;
use Symfony\Component\Form\FormView;

class TwigManager
{
    /**
     * @var TwigManagerInterface
     */
    private $manager;

    /**
     * @return TwigManagerInterface
     */
    public function getManager(): TwigManagerInterface
    {
        if (empty($this->manager) || !$this->manager instanceof TwigManagerInterface)
            throw new MissingClassException('The manager has not been set before calling the TwigManager.  Ensure your set the Manager using setManager() before you call the templates.');

        return $this->manager;
    }

    /**
     * @param TwigManagerInterface $manager
     * @return TwigManager
     */
    public function setManager(TwigManagerInterface $manager): TwigManager
    {
        $this->manager = $manager;
        return $this;
    }

    /**
     * @var \Twig_Environment
     */
    private static $twig;

    /**
     * TwigManager constructor.
     * @param \Twig_Environment $twig
     */
    public function __construct(\Twig_Environment $twig)
    {
        self::$twig = $twig;
    }

    /**
     * @param string $name
     * @param null $entity
     * @return mixed|null
     */
    public function callManagerMethod(string $name, $entity = null)
    {
        $entity = $this->parseData($entity);

        if (method_exists($this->getManager(), $name))
            return $this->getManager()->$name($entity);

        $method = 'is' . ucfirst($name);
        if (method_exists($this->getManager(), $method))
            return $this->getManager()->$method($entity);

        $method = 'get' . ucfirst($name);
        if (method_exists($this->getManager(), $method))
            return $this->getManager()->$method($entity);

        throw new MissingClassException(sprintf('The class %s does not have a method %s(), is%s() or get%s()', get_class($this->getManager()), $name, ucfirst($name), ucfirst($name)));

    }

    /**
     * @return \Twig_Environment
     * @throws MissingClassException
     */
    public function getTwig(): \Twig_Environment
    {
        return self::$twig;
    }

    /**
     * @param $entity
     * @return null|mixed
     */
    private function parseData($entity)
    {
        if ($entity instanceof FormView)
        {
            if (empty($entity->vars['value']) && empty($entity->vars['data']))
                return null;
            if (! empty($entity->vars['value']))
                return $entity->vars['value'];
            return $entity->vars['data'];
        }

        return $entity;
    }

    /**
     * renderMessage
     *
     * @param Message $message
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public static function renderMessage(Message $message): string
    {
        return self::$twig->render('Default/message.html.twig', ['message' => $message]);
    }
}