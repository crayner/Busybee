<?php
namespace App\School\Form;

use App\Core\Type\SettingChoiceType;
use App\Entity\Activity;
use App\Entity\ActivityTutor;
use App\Entity\Person;
use Hillrange\Form\Type\EntityType;
use Hillrange\Form\Type\HiddenEntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActivityTutorType extends AbstractType
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
                    'class' => Activity::class,
                ]
            )
            ->add('tutor', EntityType::class,
                [
                    'class' => Person::class,
                    'choice_label' => 'fullName',
                    'label' => 'activity_tutor.tutor.label',
                    'help' => 'activity_tutor.tutor.help',
                    'placeholder' => 'activity_tutor.tutor.placeholder',
                ]
            )
            ->add('sequence', HiddenType::class)
            ->add('id', HiddenType::class)
        ;
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			[
				'data_class'         => ActivityTutor::class,
				'translation_domain' => 'School',
			]
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'activity_tutor';
	}


}
