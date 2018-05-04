<?php
namespace App\People\Form;

use App\Calendar\Util\CalendarManager;
use App\Entity\CalendarGrade;
use App\Entity\CalendarGradeStudent;
use App\Entity\Student;
use Doctrine\ORM\EntityRepository;
use Hillrange\Form\Type\EntityType;
use Hillrange\Form\Type\EnumType;
use Hillrange\Form\Type\HiddenEntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CalendarGradeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'student_calendar_grade';
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('status', EnumType::class,
                [
                    'choice_list_class' => Student::class,
                    'choice_list_method' => 'getStatusList',
                    'choice_list_prefix' => 'student.enrolment.status',
                    'choice_translation_domain' => 'Setting',
                    'label' => 'student.calendar_grade.status.label',
                ]
            )
            ->add('calendarGrade', EntityType::class,
                [
                    'label' => 'student.calendar_grade.grade.label',
                    'help' => 'student.calendar_grade.grade.help',
                    'placeholder' => 'student.calendar_grade.grade.placeholder',
                    'class' => CalendarGrade::class,
                    'choice_label' => 'fullName',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('cg')
                            ->leftJoin('cg.calendar', 'c')
                            ->orderBy('c.firstDay', 'ASC')
                            ->addOrderBy('cg.sequence', 'ASC')
                        ;
                    },
                ]
            )
            ->add('student', HiddenEntityType::class,
                [
                    'class' => Student::class,
                ]
            )
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'translation_domain' => 'Student',
                'data_class' => CalendarGradeStudent::class,
            ]
        );
    }
}