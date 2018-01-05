<?php
namespace App\Core\Form;

use App\Entity\Calendar;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CalendarEntityType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			[
				'data_class'         => null,
				'translation_domain' => 'calendar',
				'placeholder'        => 'calendar.entity.placeholder',
				'class'              => Calendar::class,
				'choice_label'       => 'name',
				'query_builder'      => function (EntityRepository $er) {
					return $er->createQueryBuilder('c')
						->orderBy('c.name', 'DESC');
				},
			]
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'calendar';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getParent()
	{
		return EntityType::class;
	}

}
