<?php
namespace App\Timetable\Form;

use App\Entity\Timetable;
use App\Entity\TimetableColumn;
use App\Entity\TimetableDay;
use App\Timetable\Form\Subscriber\TimetableColumnSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Hillrange\Form\Type\HiddenEntityType;
use Hillrange\Form\Type\TimeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ColumnEntityType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var TimetableColumnSubscriber
     */
    private $timetableColumnSubscriber;

    /**
     * ColumnEntityType constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, TimetableColumnSubscriber $timetableColumnSubscriber)
    {
        $this->entityManager = $entityManager;
        $this->timetableColumnSubscriber = $timetableColumnSubscriber;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = ['timetable.column.mapping_info.rotate' => 'rotate'];
        $days = $this->entityManager->getRepository(TimetableDay::class)->createQueryBuilder('d')
            ->leftJoin('d.timetable', 't')
            ->select('d.name')
            ->where('t.id = :tt_id')
            ->setParameter('tt_id', $options['timetable_id'])
            ->andWhere('d.dayType = :false')
            ->setParameter('false', false)
            ->getQuery()
            ->getResult();

        foreach ($days as $day)
            $choices[$day['name']] = $day['name'];

        $builder
            ->add('name', null,
                [
                    'label' => 'timetable.column.name.label',
                    'element_class' => 'form-group-sm',
                ]
            )
            ->add('code', null,
                [
                    'label' => 'timetable.column.code.label',
                    'element_class' => 'form-group-sm',
                ]
            )
            ->add('mappingInfo', ChoiceType::class,
                [
                    'label' => 'timetable.column.mapping_info.label',
                    'help' => 'timetable.column.mapping_info.help',
                    'choices' => $choices,
                    'empty_data' => 'Rotate',
                    'element_class' => 'form-group-sm',
                ]
            )
            ->add('start', TimeType::class,
                [
                    'label' => 'timetable.column.start.label',
                    'help' => 'timetable.column.start.help',
                    'required' => false,
                    'element_class' => 'form-group-sm',
                ]
            )
            ->add('end', TimeType::class,
                [
                    'label' => 'timetable.column.end.label',
                    'help' => 'timetable.column.end.help',
                    'required' => false,
                    'element_class' => 'form-group-sm',
                ]
            )
            ->add('sequence', HiddenType::class)
            ->add('timetable', HiddenEntityType::class,
                [
                    'class' => Timetable::class,
                ]
            )
            ->add('id', HiddenType::class,
                [
                    'attr' => [
                        'class' => 'removeElement'
                    ],
                ]
            )
        ;


        $builder->addEventSubscriber($this->timetableColumnSubscriber);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => TimetableColumn::class,
                'translation_domain' => 'Timetable',
                'placeholder' => 'timetable.column.placeholder',
                'class' => TimetableColumn::class,
                'choice_label' => 'name',
                'error_bubbling' => true,
            ]
        );
        $resolver->setRequired(
            [
                'timetable_id',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'tt_columns';
    }
}