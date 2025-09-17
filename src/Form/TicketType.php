<?php

namespace App\Form;

use App\Entity\Company;
use App\Entity\Ticket;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use App\Enum\TicketStatus;

class TicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ticket_status', ChoiceType::class, [
                'label' => 'Statut',
                // enum cases as choices
                'choices' => TicketStatus::cases(),
                // view value = enum backed value
                'choice_value' => static function (?TicketStatus $status) { return $status?->value; },
                // translated label using your existing i18n keys
                'choice_label' => static function (TicketStatus $status) { return 'Ticket.status.' . $status->value; },
                'translation_domain' => 'messages',
                'choice_translation_domain' => 'messages',
                'row_attr' => ['class' => 'col-12 col-lg-3'],
            ])
            ->add('priority', ChoiceType::class, [
                'label' => 'Priorité',
                'choices' => [
                    'Ticket.priority.low' => 'low',
                    'Ticket.priority.normal' => 'normal',
                    'Ticket.priority.high' => 'high',
                    'Ticket.priority.urgent' => 'urgent',
                ],
                'translation_domain' => 'messages',
                'row_attr' => ['class' => 'col-12 col-lg-3'],
            ])
            ->add('code_folder', null, [
                'label' => 'Code dossier',
                'required' => false,
                'row_attr' => ['class' => 'col-12 col-lg-3'],
            ])
            ->add('subject', null, [
                'label' => 'Objet',
                'row_attr' => ['class' => 'col-12 col-lg-12'],
            ])
            ->add('question', null, [
                'label' => 'Question / Description',
                'row_attr' => ['class' => 'col-12'],
                'attr' => ['rows' => 12],
            ])
            ->add('due_at', null, [
                'widget' => 'single_text',
                'label' => 'Échéance',
                'required' => false,
                'row_attr' => ['class' => 'col-12 col-lg-3'],
            ])
            ->add('is_billable', CheckboxType::class, [
                'label' => 'Facturable',
                'required' => false,
                'row_attr' => ['class' => 'col-12 col-lg-12 form-check form-check-lg'],
                'label_attr' => ['class' => 'form-check-label'],
                'attr' => ['class' => 'form-check-input form-check-input-primary'],
                'data' => true,
            ])
            ->add('company_id', EntityType::class, [
                'class' => Company::class,
                'choice_label' => 'name',
                'label' => 'Société',
                'placeholder' => '— Sélectionner une société —',
                'row_attr' => ['class' => 'col-12 col-lg-6'],
            ])
            ->add('created_by_id', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
                'label' => 'Créé par',
                'row_attr' => ['class' => 'col-12 col-lg-3'],
            ])
            ->add('assigned_to_id', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
                'label' => 'Assigné à',
                'required' => false,
                'row_attr' => ['class' => 'col-12 col-lg-3'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
        ]);
    }
}
