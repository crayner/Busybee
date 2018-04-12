<?php
namespace App\Collection\Form;

use Hillrange\Form\Type\Transform\EntityToStringTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CollectionEntityType extends AbstractType
{
	/**
	 * @var EntityManagerInterface
	 */
	private $manager;

    /**
     * @var string
     */
	private $blockPrefix;

	/**
	 * HiddenEntityType constructor.
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
	    $this->blockPrefix = $options['block_prefix'];

		$builder->addModelTransformer(new EntityToStringTransformer($this->manager, $options));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return $this->blockPrefix;
	}

	public function getParent()
	{
		return \Symfony\Bridge\Doctrine\Form\Type\EntityType::class;
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setRequired(
			[
				'block_prefix',
			]
		);
        $resolver->setDefaults(
            [
                'unique_key' => 'id',
                'sort_manage' => false,
                'multiple' => false,
                'attr' => [
                    'class' => 'removeElement',
                ],
            ]
        );
	}
}