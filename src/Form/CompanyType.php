<?php

namespace App\Form;

use App\Entity\Company;
use App\Entity\CompanyStatus;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Doctrine\ORM\EntityRepository;


class CompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('companyStatus', ChoiceType::class, [
                'label' => 'Company.Status',
                'choices' => [
                    'Active' => CompanyStatus::Active,
                    'Inactive' => CompanyStatus::Inactive,
                ],
                'choice_value' => fn(?CompanyStatus $status) => $status?->value,
                'choice_label' => fn(CompanyStatus $status) => $status->name,
                'row_attr' => ['class' => 'mb-3 col-12 col-lg-4'],
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-select'],
            ])
            ->add('name', null, [
                'label' => 'Company.Name',
                'row_attr' => ['class' => 'mb-3 col-12 col-lg-6'],
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control'],
            ])
            ->add('siret', null, [
                'label' => 'Company.Siret',
                'row_attr' => ['class' => 'mb-3 col-12 col-lg-4'],
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control'],
            ])
            ->add('vatNumber', null, [
                'label' => 'Company.VatNumber',
                'row_attr' => ['class' => 'mb-3 col-12 col-lg-4'],
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control'],
            ])
            ->add('emailCompany', EmailType::class, [
                'label' => 'Company.EmailCompany',
                'row_attr' => ['class' => 'mb-3 col-12 col-lg-4'],
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control'],
            ])
            ->add('phoneCompany', null, [
                'label' => 'Company.PhoneCompany',
                'row_attr' => ['class' => 'mb-3 col-12 col-lg-4'],
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control'],
            ])
            ->add('website', null, [
                'label' => 'Company.Website',
                'row_attr' => ['class' => 'mb-3 col-12 col-lg-12'],
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control'],
            ])
            ->add('addressLine1', null, [
                'label' => 'Company.AddressLine1',
                'row_attr' => ['class' => 'mb-3 col-12 col-lg-6'],
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control'],
            ])
            ->add('addressLine2', null, [
                'label' => 'Company.AddressLine2',
                'row_attr' => ['class' => 'mb-3 col-12 col-lg-6'],
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control'],
            ])
            ->add('postalCode', null, [
                'label' => 'Company.PostalCode',
                'row_attr' => ['class' => 'mb-3 col-12 col-lg-3'],
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control'],
            ])
            ->add('city', null, [
                'label' => 'Company.City',
                'row_attr' => ['class' => 'mb-3 col-12 col-lg-6'],
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control'],
            ])
            ->add('countryCode', CountryType::class, [
                'label' => 'Company.CountryCode',
                'row_attr' => ['class' => 'mb-3 col-12 col-lg-3'],
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control'],
                'placeholder' => 'Company.Select a country',
                'preferred_choices' => ['BE', 'CA', 'FR', 'GP', 'GF', 'LU', 'MQ', 'MC', 'NC', 'PF', 'BL', 'MF', 'PM', 'CH', 'VU', 'WF'],
            ])
            ->add('notes', null, [
                'label' => 'Company.Notes',
                'row_attr' => ['class' => 'mb-3 col-12 col-lg-12'],
                'label_attr' => ['class' => 'form-label'],
                'attr' => [
                    'class' => 'form-control',
                    'rows'  => 6,
                ]
            ])
            ->add('billingContactId', EntityType::class, [
                'class' => User::class,
                'choice_label' => function (User $user) {
                    return $user->getFirstName() . ' ' . $user->getLastName();
                },
                'query_builder' => function (EntityRepository $er) use ($options) {
                    $qb = $er->createQueryBuilder('u');
                    $company = $options['data'];
                    if (!$company || !$company->getId()) {
                        return $qb->andWhere('1 = 0');
                    }
                    return $qb
                        ->andWhere('u.company = :company')
                        ->setParameter('company', $company)
                        ->orderBy('u.first_name', 'ASC')
                        ->addOrderBy('u.last_name', 'ASC');
                },
                'label' => 'Company.BillingContactId',
                'row_attr' => ['class' => 'mb-3 col-12 col-lg-4'],
                'label_attr' => ['class' => 'form-label'],
                'attr' => ['class' => 'form-control'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Company::class,
        ]);
    }
}
