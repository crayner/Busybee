<?php
namespace App\School\Form;

use App\Core\Manager\SettingManager;
use App\Core\Type\SettingChoiceType;
use Hillrange\Form\Type\EntityType;
use App\Entity\Campus;
use App\School\Form\Subscriber\CampusSubscriber;
use Hillrange\Form\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class CampusType extends AbstractType
{
	/**
	 * @var SettingManager
	 */
	private $sm;

	/**
	 * Construct
	 */
	public function __construct(SettingManager $sm)
	{
		$this->sm = $sm;
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('identifier', TextType::class, [
					'label' => 'campus.identifier.label',
					'attr'  => array(
						'class' => 'locationForm monitorChange',
					),
                    'help'  => 'campus.identifier.help',
				]
			)
			->add('name', TextType::class, [
					'label' => 'campus.name.label',
					'attr'  => array(
						'class' => 'locationForm monitorChange',
					),
                    'help'  => 'campus.name.help',
				]
			)
			->add('postcode', TextType::class, [
					'label' => 'campus.postcode.label',
					'attr'  => array(
						'class' => 'locationForm monitorChange',
					),
                    'help'  => 'campus.postcode.help',
				]
			)
			->add('territory', SettingChoiceType::class, [
					'label'    => 'campus.territory.label',
					'required' => false,
					'setting_name' => 'address.territory.list',
					'attr'     => array(
						'class' => 'locationForm monitorChange',
					),
                    'help'  => 'campus.territory.help',
				]
			)
			->add('locality', TextType::class, [
					'label' => 'campus.locality.label',
					'attr'  => array(
						'class' => 'locationForm monitorChange',
					),
                    'help'  => 'campus.locality.help',
				]
			)
			->add('country', $this->sm->get('CountryType'), [
					'label' => 'campus.country.label',
					'attr'  => array(
						'class' => 'locationForm monitorChange',
					),
                    'help'  => 'campus.country.help',
				]
			)
			->add('locationList', EntityType::class, array(
					'class'         => Campus::class,
					'attr'          => array(
						'class' => 'locationList changeRecord formChanged form-control-sm',
					),
					'label'         => false,
					'mapped'        => false,
					'choice_label'  => 'name',
					'query_builder' => function (EntityRepository $er) {
						return $er->createQueryBuilder('c')
							->orderBy('c.name', 'ASC');
					},
					'placeholder'   => 'campus.locations.placeholder',
					'required'      => false,
					'data'          => $options['data']->getId(),
				)
			);
		$builder->addEventSubscriber(new CampusSubscriber());
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class'         => Campus::class,
			'translation_domain' => 'Facility',
		));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'campus';
	}


}
