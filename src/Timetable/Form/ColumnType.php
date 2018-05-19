<?php
namespace App\Timetable\Form;

use App\Entity\TimetableColumn;
use App\Timetable\Form\Subscriber\ColumnSubscriber;
use App\Timetable\Validator\Periods;
use Hillrange\Form\Type\CollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ColumnType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => TimetableColumn::class,
                'translation_domain' => 'Timetable',
                'class' => TimetableColumn::class,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'timetable_column';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('periods', CollectionType::class,
                [
                    'allow_add' => true,
                    'allow_delete' => true,
                    'entry_type' => ColumnPeriodType::class,
                    'constraints' => [
                        new Periods(),
                    ],
                ]
            )
        ;

        $builder->addEventSubscriber(new ColumnSubscriber());
    }
}
