<?php
namespace App\Core\Subscriber;

use Psr\Container\ContainerInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageSubscriber implements EventSubscriberInterface
{
	/**
	 * @var string
	 */
	private $targetDir;

	/**
	 * ImageSubscriber constructor.
	 *
	 * @param string $targetDir
	 */
	public function __construct(ContainerInterface $container)
	{
		$this->targetDir = $container->getParameter('upload_path');
	}

	/**
	 * @return array
	 */
	public static function getSubscribedEvents()
	{
		// Tells the dispatcher that you want to listen on the form.pre_set_data
		// event and that the preSetData method should be called.
		return array(
			FormEvents::PRE_SET_DATA => 'preSetData',
			FormEvents::PRE_SUBMIT   => 'preSubmit',
		);
	}


	/**
	 * @param FormEvent $event
	 */
	public function preSetData(FormEvent $event)
	{
		$data = $event->getData();

		if (!empty($data) && !file_exists($data))
		{
			$data = null;
			$event->setData($data);
		}

	}
	/**
	 * @param FormEvent $event
	 */
	public function preSubmit(FormEvent $event)
	{
		$form = $event->getForm();
		$data = $event->getData();

		if ($data instanceof UploadedFile)
		{

			$fName = $form->getConfig()->getOption('fileName') . '_' . mb_substr(md5(uniqid()), mb_strlen($form->getConfig()->getOption('fileName')) + 1) . '.' . $data->guessExtension();

			$path = $this->targetDir;
			$data->move($path, $fName);

			$file = new File($path . DIRECTORY_SEPARATOR . $fName, true);

			$data = $file->getPathName();

			if (!empty($form->getData()) && file_exists($form->getData()))
				unlink($form->getData());
		}

		if (!empty($form->getData()) && empty($data))
			$data = $form->getData();

		$event->setData($data);

	}
}