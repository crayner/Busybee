<?php
namespace App\School\Form;

use App\Core\Util\UserManager;
use App\Entity\Calendar;
use App\Entity\CalendarGroup;
use App\Entity\RollGroup;
use App\Entity\Space;
use App\Entity\Staff;
use App\Entity\Student;
use Doctrine\ORM\EntityRepository;
use Hillrange\Form\Type\EntityType;
use Hillrange\Form\Type\HiddenEntityType;
use Hillrange\Form\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RollGroupType extends AbstractType
{
    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * RollGroupType constructor.
     * @param UserManager $userManager
     */
    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
	    $userManager = $this->userManager;
	    $data = $options['data'];
	    dump($data);
		$builder
            ->add('name', TextType::class, [
                    'label' => 'roll.name.label',
                    'help' => 'roll.name.help',
                ]
            )
            ->add('nameShort', TextType::class, [
                    'label' => 'roll.name_short.label',
                    'help' => 'roll.name_short.help',
                ]
            )
            ->add('rollTutor1', EntityType::class, [
                    'label' => 'roll.tutor1.label',
                    'help' => 'roll.tutor1.help',
                    'placeholder' => 'roll.tutor.placeholder',
                    'class' => Staff::class,
                    'choice_label' => 'formatName',
                    'query_builder' => function (EntityRepository $er) use ($userManager, $data) {
                        return $er->createQueryBuilder('s')
                            ->leftJoin('s.rollGroups1', 'r')
                            ->leftJoin('r.calendar', 'c')
                            ->where('(c.id IS NULL OR c.id = :calendar_id)')
                            ->setParameter('calendar_id', $userManager->getCurrentCalendar()->getId())
                            ->andWhere('(r.id IS NULL OR r.id = :roll_id)')
                            ->setParameter('roll_id', $data->getId())
                            ->orderBy('s.surname', 'ASC')
                            ->addOrderBy('s.firstName', 'ASC')
                        ;
                    },
                ]
            )
            ->add('rollTutor2', EntityType::class, [
                    'label' => 'roll.tutor2.label',
                    'help' => 'roll.tutor2.help',
                    'placeholder' => 'roll.tutor.placeholder',
                    'class' => Staff::class,
                    'choice_label' => 'formatName',
                    'query_builder' => function (EntityRepository $er) use ($userManager, $data) {
                        return $er->createQueryBuilder('s')
                            ->leftJoin('s.rollGroups1', 'r')
                            ->leftJoin('r.calendar', 'c')
                            ->where('(c.id IS NULL OR c.id = :calendar_id)')
                            ->setParameter('calendar_id', $userManager->getCurrentCalendar()->getId())
                            ->andWhere('(r.id IS NULL OR r.id = :roll_id)')
                            ->setParameter('roll_id', $data->getId())
                            ->orderBy('s.surname', 'ASC')
                            ->addOrderBy('s.firstName', 'ASC')
                        ;
                    },
                    'required' => false,
                ]
            )
            ->add('rollTutor3', EntityType::class, [
                    'label' => 'roll.tutor3.label',
                    'help' => 'roll.tutor3.help',
                    'placeholder' => 'roll.tutor.placeholder',
                    'class' => Staff::class,
                    'choice_label' => 'formatName',
                    'query_builder' => function (EntityRepository $er) use ($userManager, $data) {
                        return $er->createQueryBuilder('s')
                            ->leftJoin('s.rollGroups1', 'r')
                            ->leftJoin('r.calendar', 'c')
                            ->where('(c.id IS NULL OR c.id = :calendar_id)')
                            ->setParameter('calendar_id', $userManager->getCurrentCalendar()->getId())
                            ->andWhere('(r.id IS NULL OR r.id = :roll_id)')
                            ->setParameter('roll_id', $data->getId())
                            ->orderBy('s.surname', 'ASC')
                            ->addOrderBy('s.firstName', 'ASC')
                        ;
                    },
                    'required' => false,
                ]
            )
            ->add('website', UrlType::class, [
                    'label' => 'roll.website.label',
                    'required' => false,
                ]
            )
            ->add('calendar', HiddenEntityType::class, [
                    'class' => Calendar::class,
                ]
            )
            ->add('space', EntityType::class,
                [
                    'class' => Space::class,
                    'label' => 'roll.space.label',
                    'placeholder' => 'roll.space.placeholder',
                    'choice_label' => 'name',
                ]
            )
            ->add('students', EntityType::class, [
                    'label' => 'roll.students.label',
                    'help' => 'roll.students.help',
                    'multiple' => true,
                    'expanded' => true,
                    'class' => Student::class,
                    'choice_label' => 'formatName',
                    'query_builder' => function (EntityRepository $er) use ($data, $userManager) {
                        return $er->createQueryBuilder('s')
                            ->leftJoin('s.rollGroups', 'r')
                            ->leftJoin('r.calendar', 'c')
                            ->where('c.id = :calendar_id')
                            ->setParameter('calendar_id', $userManager->getCurrentCalendar()->getId())
                            ->andWhere('(r.id IS NULL OR r.id = :roll_id)')
                            ->setParameter('roll_id', $data->getId())
                            ->orderBy('s.surname', 'ASC')
                            ->addOrderBy('s.firstName', 'ASC')
                            ;
                    },
                ]
            )
        ;
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			[
				'data_class'         => RollGroup::class,
				'translation_domain' => 'School',
			]
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'roll';
	}
}
