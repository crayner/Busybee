<?php
namespace App\Core\Type;

use App\Core\Transformer\ToggleTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormInterface;

class ToggleType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$emptyData = function (FormInterface $form, $viewData) {
			return $viewData;
		};

		$resolver->setDefaults(
			array(
				'value'      => '1',
				'empty_data' => $emptyData,
				'compound'   => false,
				'required'   => false,
				'div_class'  => 'toggleRight'
			)
		);
	}

	/**
	 * @return string
	 */
	public function getParent()
	{
		return CheckboxType::class;
	}

	/**
	 * @return string
	 */
	public function getBlockPrefix()
	{
		return 'toggle';
	}

	/**
	 * @param FormBuilderInterface $builder
	 * @param array                $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->addModelTransformer(new ToggleTransformer());
	}

	/**
	 * @param FormView      $view
	 * @param FormInterface $form
	 * @param array         $options
	 */
	public function buildView(FormView $view, FormInterface $form, array $options)
	{
		$view->vars = array_replace($view->vars,
			array(
				'div_class' => $options['div_class'],
			)
		);
	}
}