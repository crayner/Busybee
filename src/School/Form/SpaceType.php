<?php
namespace App\School\Form;

use App\Core\Manager\SettingManager;
use App\Core\Type\SettingChoiceType;
use Hillrange\Form\Type\ToggleType;
use App\Entity\Campus;
use App\Entity\Space;
use App\Entity\Staff;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SpaceType extends AbstractType
{
	/**
	 * @var EntityManagerInterface
	 */
	private $manager;

	/**
	 * @var SettingManager
	 */
	private $sm;

	/**
	 * Construct
	 */
	public function __construct(EntityManagerInterface $manager, SettingManager $sm)
	{
		$this->manager = $manager;
		$this->sm      = $sm;
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$person_id = empty($options['data']->getStaff()) ? null : $options['data']->getStaff()->getPerson()->getId();
		$builder
			->add('name', null, array(
					'label' => 'space.name.label',
					'attr'  => array(
						'class' => 'monitorChange',
					),
				)
			)
			->add('type', SettingChoiceType::class, array(
					'label'       => 'space.type.label',
					'placeholder' => 'space.type.placeholder',
					'attr'        => array(
						'class' => 'monitorChange',
					),
                    'setting_name' => 'space.type',
                    'sort_choice' => false,
				)
			)
			->add('capacity', IntegerType::class, array(
					'label'      => 'space.capacity.label',
					'attr'       => array(
						'min'   => '0',
						'max'   => '9999',
						'class' => 'monitorChange',
					),
					'help'  => 'space.capacity.help',
					'empty_data' => '0',
				)
			)
			->add('computer', ToggleType::class, array(
					'label' => 'space.computer.label',
					'attr'  => array(
						'class' => 'monitorChange',
					),
				)
			)
			->add('studentComputers', IntegerType::class, array(
					'label'      => 'space.studentComputers.label',
					'attr'       => array(
						'min'   => '0',
						'max'   => '999',
						'class' => 'monitorChange',
					),
					'empty_data' => '0',
				)
			)
			->add('projector', ToggleType::class, array(
					'label' => 'space.projector.label',
					'attr'  => array(
						'class' => 'monitorChange',
					),
				)
			)
			->add('tv', ToggleType::class, array(
					'label' => 'space.tv.label',
					'attr'  => array(
						'class' => 'monitorChange',
					),
				)
			)
			->add('dvd', ToggleType::class, array(
					'label' => 'space.dvd.label',
					'attr'  => array(
						'class' => 'monitorChange',
					),
				)
			)
			->add('hifi', ToggleType::class, array(
					'label' => 'space.hifi.label',
					'attr'  => array(
						'class' => 'monitorChange',
					),
                )
			)
			->add('speakers', ToggleType::class, array(
					'label' => 'space.speakers.label',
					'attr'  => array(
						'class' => 'monitorChange',
					),
				)
			)
			->add('iwb', ToggleType::class, array(
					'label' => 'space.iwb.label',
					'attr'  => array(
						'class' => 'monitorChange',
					),
				)
			)
			->add('duplicateid', HiddenType::class, [
					'mapped' => false,
				]
			)
			->add('phoneInt', null, array(
					'label' => 'space.phoneint.label',
					'attr'  => array(
						'class' => 'monitorChange',
					),
				)
			)
			->add('phoneExt', null, array(
					'label' => 'space.phoneext.label',
					'attr'  => array(
						'class' => 'monitorChange',
					),
				)
			)
			->add('comment', TextareaType::class, array(
					'label'    => 'space.comment.label',
					'required' => false,
					'attr'     => array(
						'class' => 'monitorChange',
					),
				)
			)
			->add('staff', EntityType::class,
				array(
					'class'         => Staff::class,
					'choice_label'  => 'formatName',
					'choice_value'  => 'id',
					'label'         => 'space.staff.label',
					'placeholder'   => 'space.staff.placeholder',
					'empty_data'    => null,
					'attr'          => array(
						'class' => 'monitorChange',
					),
					'help'  => 'space.staff.help',
					'required'      => false,
					'query_builder' => function (EntityRepository $er) use ($person_id) {
						return $er->createQueryBuilder('s')
							->leftJoin('s.homeroom', 'h')
							->orderBy('s.surname', 'ASC')
							->addOrderBy('s.firstName', 'ASC')
							->where('s.id = :person_id')
							->setParameter('person_id', $person_id)
							->orWhere('h.staff IS NULL');
					},
				)
			)
			->add('campus', EntityType::class,
				array(
					'label'        => 'space.campus.label',
					'help'  => 'space.campus.help',
					'attr'         => [
						'class' => 'monitorChange',
					],
					'class'        => Campus::class,
					'choice_label' => 'name',
					'choice_value' => 'id',
					'empty_data'   => $this->manager->getRepository(Campus::class)->find(1),
					'placeholder'  => 'space.campus.placeholder',
				)
			)
			->add('changeRecord', EntityType::class,
				array(
					'label'         => false,
					'attr'          => array(
						'class' => 'formChanged changeRecord form-control-sm',
					),
					'class'         => Space::class,
					'choice_label'  => 'fullName',
					'choice_value'  => 'id',
					'mapped'        => false,
					'required'      => false,
					'placeholder'   => 'space.changeRecord.placeholder',
					'query_builder' => function (EntityRepository $er) {
						return $er->createQueryBuilder('s')
							->orderBy('s.name', 'ASC');
					},
				)
			);
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			[
				'data_class'         => Space::class,
				'translation_domain' => 'Facility',
			]
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'space';
	}


}
