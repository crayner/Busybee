<?php
namespace App\Timetable\Form;

use App\Entity\Timetable;
use App\Entity\TimetableColumn;
use App\Entity\TimetableDay;
use Doctrine\ORM\EntityRepository;
use Hillrange\Form\Type\EntityType;
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
        $tt_id = $options['timetable_id'];
        $builder
            ->add('name', TextType::class,
                [

                ]
            )
            ->add('code', TextType::class,
                [

                ]
            )
            ->add('colour', ColorType::class,
                [
                    'required' => false,
                ]
            )
            ->add('fontColour', ColorType::class,
                [
                    'required' => false,
                ]
            )
            ->add('timetable', HiddenEntityType::class,
                [
                    'class' => Timetable::class,
                ]
            )
            ->add('column', EntityType::class,
                [
                    'class' => TimetableColumn::class,
                    'choice_label' => 'name',
                    'placeholder' => 'timetable.day.column.placeholder',
                    'query_builder' => function(EntityRepository $er) use ($tt_id) {
                        return $er->createQueryBuilder('tc')
                            ->leftJoin('tc.timetable', 't')
                            ->orderBy('tc.sequence', 'ASC')
                            ->where('t.id = :tt_id')
                            ->setParameter('tt_id', $tt_id)
                        ;
                    },
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
                    'data_class' => TimetableDay::class,
                ]
            )
        ;
        $resolver
            ->setRequired(
                [
                    'timetable_id',
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