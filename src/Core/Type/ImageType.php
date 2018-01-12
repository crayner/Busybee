<?php
namespace App\Core\Type;

use App\Core\Type\Transform\ImageToStringTransformer;
use App\Core\Subscriber\ImageSubscriber;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ImageType extends AbstractType
{
	/**
	 * @var ImageSubscriber
	 */
	private $imageSubscriber;

	/**
	 * ImageSubscriber constructor.
	 *
	 * @param ImageSubscriber $imageSubscriber
	 */
	public function __construct(ImageSubscriber $imageSubscriber)
	{
		$this->imageSubscriber = $imageSubscriber;
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			[
				'compound'     => false,
				'multiple'     => false,
				'type'         => 'file',
				'deleteTarget' => '_self',
				'deleteParams' => null,
			]
		);

		$resolver->setRequired(
			[
				'deletePhoto',
				'deleteTarget',
				'deleteParams',
				'fileName',
			]
		);
	}

	/**
	 * @return string
	 */
	public function getBlockPrefix()
	{
		return 'image';
	}

	/**
	 * @return mixed
	 */
	public function getParent()
	{
		return FileType::class;
	}

	/**
	 * @param FormBuilderInterface $builder
	 * @param array                $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->addModelTransformer(new ImageToStringTransformer());
		$builder->addEventSubscriber($this->imageSubscriber);

	}

	/**
	 * @param FormView      $view
	 * @param FormInterface $form
	 * @param array         $options
	 */
	public function buildView(FormView $view, FormInterface $form, array $options)
	{
		$view->vars['deletePhoto']  = $options['deletePhoto'];
		$view->vars['deleteTarget'] = $options['deleteTarget'];
		$view->vars['deleteParams'] = $options['deleteParams'];
	}
}