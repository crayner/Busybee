<?php
namespace App\School\Form;

use App\Entity\Course;
use Hillrange\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DepartmentCourseType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', EntityType::class,
                [
                    'class' => Course::class,
                    'choice_label' => 'fullName',
                    'label' => false,
                    'placeholder' => 'department.course.name.placeholder',
                ]
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'department_course';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class'         => Course::class,
                'translation_domain' => 'School',
            ]
        );
    }
}