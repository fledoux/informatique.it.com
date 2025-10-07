@extends('emails.global')

@section('content')
<h2 style="color: #dc3545; margin-bottom: 20px;">Accès refusé</h2>

<p>Bonjour,</p>

<p>Vous avez tenté de répondre au ticket <strong>#{{ $ticketId }}</strong> depuis l'adresse email <strong>{{ $senderEmail }}</strong>.</p>

<p style="color: #dc3545; font-weight: bold;">Votre réponse n'a pas pu être traitée car vous n'êtes pas inscrit dans notre système.</p>

<h3>Pour pouvoir répondre aux tickets, vous devez :</h3>

<div style="background-color: #f8f9fa; padding: 20px; border-left: 4px solid #007bff; margin: 20px 0;">
    <p><strong>1. Créer un compte :</strong></p>
    <p><a href="{{ $registerUrl }}" style="background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">S'inscrire maintenant</a></p>
    
    <p style="margin-top: 20px;"><strong>2. Ou vous connecter si vous avez déjà un compte :</strong></p>
    <p><a href="{{ $loginUrl }}" style="background-color: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">Se connecter</a></p>
</div>

<p><strong>Une fois inscrit et connecté</strong>, vous pourrez :</p>
<ul>
    <li>Voir tous vos tickets</li>
    <li>Répondre par email ou via l'interface web</li>
    <li>Suivre l'avancement de vos demandes</li>
</ul>

<p>Si vous pensez qu'il s'agit d'une erreur, contactez-nous directement à cette adresse.</p>

<p>Cordialement,<br>
L'équipe Support<br>
<strong>Informatique IT</strong></p>
@endsection