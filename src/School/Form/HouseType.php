<?php
namespace App\School\Form;

use Hillrange\Form\Type\ImageType;
use Hillrange\Form\Type\TextType;
use App\School\Entity\House;
use App\School\Form\Subscriber\HouseSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;

class HouseType extends AbstractType
{
	/**
	 * @var HouseSubscriber
	 */
	private $houseSubscriber;

	/**
	 * HouseType constructor.
	 *
	 * @param HouseSubscriber $houseSubscriber
	 */
	public function __construct(HouseSubscriber $houseSubscriber)
	{
		$this->houseSubscriber = $houseSubscriber;
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('name', TextType::class,
				[
					'label'       => 'school.house.name.label',
					'constraints' => [
						new NotBlank(),
					],
				]
			)
			->add('shortName', TextType::class,
				[
					'label'       => 'school.house.shortname.label',
					'constraints' => [
						new NotBlank(),
					],
				]
			)
			->add('logo', ImageType::class,
				[
					'label'       => 'school.house.logo.label',
					'attr'        => [
						'help'       => 'school.house.logo.help',
						'imageClass' => 'smallLogo',
					],
					'constraints' => [
						new Image(['maxRatio' => 1.25, 'minRatio' => 0.75, 'maxSize' => '750k']),
					],
					'required'    => false,
					'deletePhoto' => $options['deletePhoto'],
					'fileName'    => 'house',
				]
			);
		$builder->addEventSubscriber($this->houseSubscriber);
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			[
				'translation_domain' => 'School',
				'data_class'         => House::class,
			]
		);
		$resolver->setRequired(
			[
				'deletePhoto',
			]
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'house';
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
