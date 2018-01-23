<?php
namespace App\School\Form;

use App\School\Util\HouseManager;
use App\School\Validator\Houses;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HousesType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('houses', CollectionType::class,
				[
					'entry_type'    => HouseType::class,
					'attr'          => [
						'class' => 'houseCollection',
					],
					'allow_add'     => true,
					'allow_delete'  => true,
					'constraints'   => [
						new Houses(),
					],
					'entry_options' => [
						'deletePhoto' => $options['deletePhoto'],
					],
				]
			);
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'translation_domain' => 'School',
			'data_class'         => HouseManager::class,
		));
		$resolver->setRequired([
			'deletePhoto',
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'houses_manage';
	}

	/**
	 * @param FormView      $view
	 * @param FormInterface $form
	 * @param array         $options
	 */
	public function buildView(FormView $view, FormInterface $form, array $options)
	{
		$view->vars['deletePhoto'] = $options['deletePhoto'];
	}
}
