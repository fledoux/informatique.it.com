<?php
// src/Form/UserType.php
namespace App\Form;

use App\Entity\User;
use App\Entity\Company;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'User.Email',
                'row_attr'   => ['class' => 'mb-3 col-12 col-lg-4'],
                'label_attr' => ['class' => 'form-label'],
                'attr'       => ['class' => 'form-control'],
            ])
            ->add('firstName', TextType::class, [
                'label' => 'User.FirstName',
                'required' => true,
                'row_attr'   => ['class' => 'mb-3 col-12 col-lg-4'],
                'label_attr' => ['class' => 'form-label'],
                'attr'       => ['class' => 'form-control'],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'User.LastName',
                'required' => true,
                'row_attr'   => ['class' => 'mb-3 col-12 col-lg-4'],
                'label_attr' => ['class' => 'form-label'],
                'attr'       => ['class' => 'form-control'],
            ])
            ->add('phone', TelType::class, [
                'label' => 'User.Phone',
                'required' => false,
                'row_attr'   => ['class' => 'mb-3 col-12 col-lg-4'],
                'label_attr' => ['class' => 'form-label'],
                'attr'       => ['class' => 'form-control'],
            ])
            ->add('company', EntityType::class, [
                'class' => Company::class,
                'choice_label' => 'name',
                'placeholder' => 'User.—',
                'required' => false,
                'label' => 'User.Company',
                'row_attr'   => ['class' => 'mb-3 col-12 col-lg-6'],
                'label_attr' => ['class' => 'form-label'],
                'attr'       => ['class' => 'form-select'],
            ])
            ->add('userStatus', ChoiceType::class, [
                'label' => 'User.Status',
                'choices' => [
                    'User.Active' => 'active',
                    'User.Inactive' => 'inactive',
                ],
                'placeholder' => 'User.Select',
                'required' => false,
                'row_attr'   => ['class' => 'mb-3 col-12 col-lg-3'],
                'label_attr' => ['class' => 'form-label'],
            ])
            ->add('password', PasswordType::class, [
                'label' => 'User.Password',
                'required' => true,
                'mapped' => false, // do not map directly to entity so setPassword(null) is never called
                'empty_data' => '', // when left empty, the form returns an empty string instead of null
                'row_attr'   => ['class' => 'mb-3 col-12 col-lg-4'],
                'label_attr' => ['class' => 'form-label'],
                'attr'       => ['class' => 'form-control'],
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'User.Roles',
                'choices' => [
                    'User.User'          => 'ROLE_USER',
                    'User.Administrator' => 'ROLE_ADMIN',
                    'User.SuperAdmin'    => 'ROLE_SUPER_ADMIN',
                ],
                'multiple' => true,
                'expanded' => true, // cases à cocher
                'row_attr'   => ['class' => 'mb-3 col-12'],
                'label_attr' => ['class' => 'form-label'],
            ])
            ->add('isVerified', CheckboxType::class, [
                'label' => 'User.IsVerified',
                'required' => false,
                'row_attr'   => ['class' => 'mb-3 col-12 col-lg-12'],
                'label_attr' => ['class' => 'form-check-label'],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'User.AgreeTerms',
                'required' => false,
                'row_attr'   => ['class' => 'mb-3 col-12 col-lg-12'],
                'label_attr' => ['class' => 'form-check-label'],
            ])
            ->add('channels', ChoiceType::class, [
                'label' => 'User.Channels',
                'choices' => [
                    'Email' => 'email',
                    'SMS'   => 'sms',
                ],
                'multiple' => true,
                'expanded' => true, // cases à cocher
                'required' => false,
                'row_attr'   => ['class' => 'mb-3 col-12'],
                'label_attr' => ['class' => 'form-label'],
            ])
            ->add('note', TextareaType::class, [
                'label' => 'User.Note',
                'required' => false,
                'row_attr'   => ['class' => 'mb-3 col-12'],
                'label_attr' => ['class' => 'form-label'],
                'attr'       => ['class' => 'form-control', 'rows' => 6],
            ])
            ->add('lastLoginAt', TextType::class, [
                'label' => 'User.LastLoginAt',
                'mapped' => false,
                'required' => false,
                'disabled' => true,
                'data' => $options['data']?->getLastLoginAt()?->format('Y-m-d H:i:s'),
                'row_attr'   => ['class' => 'mb-3 col-12 col-lg-4 opacity-25'],
                'label_attr' => ['class' => 'form-label'],
            ])
            ->add('createdAt', TextType::class, [
                'label' => 'User.CreatedAt',
                'mapped' => false,
                'required' => false,
                'disabled' => true,
                'data' => $options['data']?->getCreatedAt()?->format('Y-m-d H:i:s'),
                'row_attr'   => ['class' => 'mb-3 col-12 col-lg-4 opacity-25'],
                'label_attr' => ['class' => 'form-label'],
            ])
            ->add('updatedAt', TextType::class, [
                'label' => 'User.UpdatedAt',
                'mapped' => false,
                'required' => false,
                'disabled' => true,
                'data' => $options['data']?->getUpdatedAt()?->format('Y-m-d H:i:s'),
                'row_attr'   => ['class' => 'mb-3 col-12 col-lg-4 opacity-25'],
                'label_attr' => ['class' => 'form-label'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => User::class]);
    }
}
