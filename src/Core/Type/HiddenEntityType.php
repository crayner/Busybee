<?php
namespace App\Core\Type;

use App\Core\Form\Transformer\EntityToStringTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HiddenEntityType extends AbstractType
{
	/**
	 * @var EntityManagerInterface
	 */
	private $manager;

	/**
	 * StaffType constructor.
	 *
	 * @param EntityManagerInterface $manager
	 */
	public function __construct(EntityManagerInterface $manager)
	{
		$this->manager = $manager;
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->addModelTransformer(new EntityToStringTransformer($this->manager, $options));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'bee_entity_hidden';
	}

	public function getParent()
	{
		return HiddenType::class;
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setRequired(
			[
				'class',
			]
		);
		$resolver->setDefaults(
			[
				'multiple' => false,
			]
		);
	}
}