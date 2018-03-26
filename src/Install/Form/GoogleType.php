<?php
namespace App\Install\Form;

use Hillrange\Form\Type\TextType;
use Hillrange\Form\Type\ToggleType;
use Symfony\Component\Form\FormBuilderInterface;

class GoogleType
{

    static public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('googleOAuth', ToggleType::class,
                [
                    'label' => 'misc.google.oauth.label',
                    'help' => 'misc.google.oauth.help',
                    'translation_domain' => 'Install',
                ]
            )
            ->add('googleClientId', TextType::class,
                [
                    'label' => 'misc.google.client_id.label',
                    'attr' => array(
                        'class' => 'googleSetting',
                    ),
                    'help' => 'misc.google.client_id.help',
                    'required' => false,
                    'translation_domain' => 'Install',
                ]
            )
            ->add('googleClientSecret', TextType::class,
                [
                    'label' => 'misc.google.client_secret.label',
                    'attr' => array(
                        'class' => 'googleSetting',
                    ),
                    'help' => 'misc.google.client_secret.help',
                    'required' => false,
                    'translation_domain' => 'Install',
                ]
            )
        ;
    }
}