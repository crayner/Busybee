<?php
namespace App\School\Form;

use App\Core\Organism\Collection;
use Hillrange\Form\Type\CollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FacilityCollectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('values', CollectionType::class,
                [
                    'entry_type' => FacilityType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'unique_key' => 'name',
                    'removal_warning' => true,
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Collection::class,
                'translation_domain' => 'School',
            ]
        );
    }
}