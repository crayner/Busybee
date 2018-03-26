<?php
namespace App\Core\Form;

use App\Core\Organism\ThirdParty;
use App\Install\Form\GoogleType;
use App\Install\Form\MailerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ThirdPartyType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        GoogleType::buildForm($builder, $options);

        MailerType::buildMailerForm($builder, $options);

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => ThirdParty::class,
                'translation_domain' => 'System',
            ]
        );
    }

    /**
     * @return null|string
     */
    public function getBlockPrefix()
    {
        return 'third_party';
    }
}