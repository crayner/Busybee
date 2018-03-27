<?php
namespace App\Timetable\Form;

use App\Entity\Timetable;
use App\Entity\TimetableDay;
use Hillrange\Form\Type\HiddenEntityType;
use Hillrange\Form\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TimetableDayType extends AbstractType
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
            )->add('colour', ColorType::class,
                [
                    'required' => false,
                ]
            )->add('fontColour', ColorType::class,
                [
                    'required' => false,
                ]
            )->add('timetable', HiddenEntityType::class,
                [
                    'class' => Timetable::class,
                ]
            )->add('sequence', HiddenType::class)
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
                    'data_class' => TimetableDay::class,
                ]
            )
        ;
    }

    /**
     * @return null|string
     */
    public function getBlockPrefix()
    {
        return 'tt_day';
    }
}