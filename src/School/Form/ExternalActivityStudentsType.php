<?php
namespace App\School\Form;

use App\Core\Type\SettingChoiceType;
use App\Entity\ActivityStudent;
use App\Entity\Student;
use App\People\Util\StudentManager;
use Hillrange\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
                    'label' => 'activity.student.external.student.label',
                    'placeholder' => 'activity.student.external.student.placeholder',
                    'choice_label' => 'fullName',
                    'class' => Student::class,
                    'attr' => [
                        'class' => 'form-control-sm',
                    ],
                    'query_builder' => $options['student_list'],
                    'choice_value' => 'id',
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
