<?php
namespace App\School\Form;

use App\Core\Type\SettingChoiceType;
use App\Entity\ActivityStudent;
use App\Entity\ExternalActivity;
use App\Entity\Student;
use Hillrange\Form\Type\EntityType;
use Hillrange\Form\Type\HiddenEntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExternalActivityStudentsType extends AbstractType
{
    /**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
	    $builder
            ->add('student', EntityType::class,
                [
                    'class' => Student::class,
                    'choice_label' => 'fullName',
                    'choice_value' => 'id',
                    'label' => 'activity_student.student.label',
                    'help' => 'activity_student.student.help',
                    'placeholder' => 'activity_student.student.placeholder',
                    'choices' => $options['student_list'],
                    'attr' => [
                        'class' => 'form-control-sm',
                    ],
                ]
            )
            ->add('externalStatus', SettingChoiceType::class,
                [
                    'setting_name' => 'external.activity.status.list',
                    'label' => 'activity.student.external.status.label',
                    'placeholder' => 'activity.student.external.status.placeholder',
                    'empty_data' => 'pending',
                    'required' => false,
                    'attr' => [
                        'class' => 'form-control-sm',
                    ],
                ]
            )
            ->add('activity', HiddenEntityType::class,
                [
                    'class' => ExternalActivity::class,
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
				'data_class'         => ActivityStudent::class,
				'translation_domain' => 'School',
			]
		);
		$resolver->setRequired(
		    [
		        'student_list',
            ]
        );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'external_activity_student';
	}
}
