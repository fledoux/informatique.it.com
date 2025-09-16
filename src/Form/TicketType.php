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

class TicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ticket_status', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'Ticket.status.new' => 'new',
                    'Ticket.status.in_progress' => 'in_progress',
                    'Ticket.status.waiting' => 'waiting',
                    'Ticket.status.resolved' => 'resolved',
                    'Ticket.status.closed' => 'closed',
                    'Ticket.status.canceled' => 'canceled',
                ],
                'translation_domain' => 'messages',
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
            ])
            ->add('code_folder', null, [
                'label' => 'Code dossier',
                'required' => false,
            ])
            ->add('subject', null, [
                'label' => 'Objet',
            ])
            ->add('question', null, [
                'label' => 'Question / Description',
            ])
            ->add('due_at', null, [
                'widget' => 'single_text',
                'label' => 'Échéance',
                'required' => false,
            ])
            ->add('is_billable', null, [
                'label' => 'Facturable',
                'required' => false,
            ])
            ->add('company_id', EntityType::class, [
                'class' => Company::class,
                'choice_label' => 'name',
                'label' => 'Société',
                'placeholder' => '— Sélectionner une société —',
            ])
            ->add('created_by_id', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
                'label' => 'Créé par',
            ])
            ->add('assigned_to_id', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
                'label' => 'Assigné à',
                'required' => false,
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
