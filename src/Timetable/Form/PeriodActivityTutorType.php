<?php
namespace App\Timetable\Form;

use App\Core\Type\SettingChoiceType;
use App\Entity\TimetablePeriodActivity;
use App\Entity\TimetablePeriodActivityTutor;
use App\Entity\Staff;
use Doctrine\ORM\EntityRepository;
use Hillrange\Form\Type\EntityType;
use Hillrange\Form\Type\HiddenEntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PeriodActivityTutorType extends AbstractType
{
    /**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
            ->add('role', SettingChoiceType::class,
                [
                    'label' => 'activity_tutor.role.label',
                    'help' => 'activity_tutor.role.help',
                    'placeholder' => 'activity_tutor.role.placeholder',
                    'setting_name' => 'tutor.type.list',
                ]
            )
            ->add('activity', HiddenEntityType::class,
                [
                    'class' => TimetablePeriodActivity::class,
                ]
            )
            ->add('tutor', EntityType::class,
                [
                    'class' => Staff::class,
                    'choice_label' => 'fullName',
                    'label' => 'activity_tutor.tutor.label',
                    'help' => 'activity_tutor.tutor.help',
                    'placeholder' => 'activity_tutor.tutor.placeholder',
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('p')
                            ->orderBy('p.surname')
                            ->addOrderBy('p.firstName')
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
				'data_class'         => TimetablePeriodActivityTutor::class,
				'translation_domain' => 'School',
			]
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'period_activity_tutor';
	}
}
