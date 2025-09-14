<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Contact.Name',
                'attr'  => ['placeholder' => 'Contact.NameExample'],
                'required' => true
            ])
            ->add('email', EmailType::class, [
                'label' => 'Contact.Email',
                'attr'  => ['placeholder' => 'Contact.MailExample'],
                'required' => true
            ])
            ->add('phone', TelType::class, [
                'label' => 'Contact.Phone',
                'attr'  => ['placeholder' => 'Contact.PhoneExample'],
                'required' => true
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Contact.WhatKind',
                'choices' => [
                    'Contact.Type.Individual' => 'Particulier',
                    'Contact.Type.Association' => 'Association',
                    'Contact.Type.Company' => 'Entreprise',
                    'Contact.Type.Collectivity' => 'Collectivité',
                ],
                'placeholder' => 'Contact.Select',
                'required' => true
            ])
            ->add('need', TextareaType::class, [
                'label' => 'Contact.Need',
                'attr'  => ['rows' => 4, 'placeholder' => 'Contact.NeedExample'],
                'required' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
