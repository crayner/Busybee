<?php
namespace App\Core\Type;

use App\Core\Form\Transformer\EntityToStringTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class EntityType extends AbstractType
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
		return 'bee_entity';
	}

	public function getParent()
	{
		return \Symfony\Bridge\Doctrine\Form\Type\EntityType::class;
	}
}