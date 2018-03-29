<?php
namespace App\Timetable\Form;

use App\Entity\Timetable;
use App\Entity\TimetableColumn;
use Hillrange\Form\Type\CollectionType;
use Hillrange\Form\Type\HiddenEntityType;
use Hillrange\Form\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TimetableColumnType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class,
                [

                ]
            )->add('code', TextType::class,
                [

                ]
            )->add('timetable', HiddenEntityType::class,
                [
                    'class' => Timetable::class,
                ]
            )
            ->add('sequence', HiddenType::class)
            ->add('id', HiddenType::class)
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(
                [
                    'transDomain' => 'Timetable',
                    'data_class' => TimetableColumn::class,
                ]
            )
        ;
    }

    /**
     * @return null|string
     */
    public function getBlockPrefix()
    {
        return 'tt_column';
    }
}