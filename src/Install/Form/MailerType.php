<?php
namespace App\Install\Form;

use Hillrange\Form\Type\TextType;
use App\Install\Form\Subscriber\MailerSubscriber;
use App\Install\Organism\Mailer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class MailerType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('transport', ChoiceType::class,
				[
					'label'   => 'mailer.transport.label',
					'choices' => [
						'mailer.transport.placeholder' => 'off',
						'mailer.transport.smtp'        => 'smtp',
						'mailer.transport.mail'        => 'mail',
						'mailer.transport.sendmail'    => 'sendmail',
						'mailer.transport.gmail'       => 'gmail',
					],
						'help' => 'mailer.transport.help',
				]
			)
			->add('host', TextType::class,
				[
					'label'       => 'mailer.host.label',
					'attr'        => array(
						'class' => 'smtpMailer',
					),
					'help'  => 'mailer.host.help',
					'constraints' => [
						new NotBlank(),
					],
				]
			)
			->add('port', TextType::class,
				[
					'label'    => 'mailer.port.label',
					'attr'     => array(
						'class' => 'smtpMailer',
					),
					'help'  => 'mailer.port.help',
					'required' => false,
				]
			)
			->add('encryption', ChoiceType::class,
				[
					'label'    => 'mailer.encryption.label',
					'mapped'   => false,
					'choices'  => [
						'mailer.encryption.none' => 'none',
						'mailer.encryption.ssl'  => 'ssl',
						'mailer.encryption.tls'  => 'tls',
					],
					'attr'     => [
						'class' => 'smtpMailer',
					],
					'help'  => 'mailer.encryption.help',
					'required' => false,
				]
			)
			->add('auth_mode', ChoiceType::class,
				[
					'label'    => 'mailer.auth_mode.label',
					'mapped'   => false,
					'choices'  => [
						'mailer.auth_mode.plain'    => 'plain',
						'mailer.auth_mode.login'    => 'lodin',
						'mailer.auth_mode.cram-md5' => 'cram-md5',
					],
					'attr'     => [
						'class' => 'smtpMailer',
					],
					'help'  => 'mailer.auth_mode.help',
					'required' => false,
				]
			)
			->add('user', TextType::class,
				[
					'label'       => 'mailer.user.label',
					'attr'        => array(
						'class' => 'mailerDetails',
					),
					'help'  => 'mailer.user.help',
					'constraints' => [
						new NotBlank(),
					],
				]
			)
			->add('password', TextType::class,
				[
					'label'       => 'mailer.password.label',
					'attr'        => array(
						'class' => 'mailerDetails',
					),
					'help'  => 'mailer.password.help',
					'constraints' => [
						new NotBlank(),
					],
				]
			)
			->add('senderName', TextType::class,
				[
					'label'       => 'mailer.sender_name.label',
					'attr'        => array(
						'class' => 'mailerDetails',
					),
					'help'  => 'mailer.sender_name.help',
					'constraints' => [
						new NotBlank(),
					],
				]
			)
			->add('senderAddress', EmailType::class,
				[
					'label'       => 'mailer.sender_address.label',
					'attr'        => array(
						'class' => 'mailerDetails',
					),
					'help'  => 'mailer.sender_address.help',
					'constraints' => [
						new NotBlank(),
						new Email(),
					],
				]
			)
		;
		$builder->addEventSubscriber(new MailerSubscriber());
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'translation_domain' => 'Install',
			'data_class'         => Mailer::class,
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'install_mailer';
	}


}
