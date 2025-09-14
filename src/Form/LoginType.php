<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('_username', TextType::class, [
                'label' => 'Login.Login',
                'row_attr' => ['class' => 'form-floating'],
                'attr' => ['placeholder' => ' '],
            ])
            ->add('_password', PasswordType::class, [
                'label' => 'Login.Password',
                'row_attr' => ['class' => 'form-floating'],
                'attr' => ['placeholder' => ' '],
            ])
            ->add('_remember_me', CheckboxType::class, [
                'label' => 'Login.Remember me',
                'required' => false,
                'row_attr' => ['class' => 'form-check p-0 mb-3'],
                'label_attr' => ['class' => 'form-check-label'],
                'attr' => ['class' => 'form-check-input'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false, // on gère le token manuellement dans le template
        ]);
    }

    public function getBlockPrefix(): string
    {
        return ''; // pas de préfixe → _username / _password compatibles avec form_login
    }
}