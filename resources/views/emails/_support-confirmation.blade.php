<p>{{ \App\Helpers\Helper::getGreeting() }} {{ $ticket->author->firstname ?? '' }},</p>

<p>
	Nous vous confirmons la bonne réception de votre demande de support concernant :</p>
	<blockquote style="border-left: 4px solid #ff4c00; padding-left: 12px;">"{{ $ticket->subject ?? '' }}"</blockquote>
	<p>Notre équipe technique a bien été informée et examinera votre requête sous peu.
</p>

<p>
	Votre demande fait l’objet d’une prestation de service (consommation de ticket*). Dans une optique d’efficacité et de
    bonne gestion des couts, nous vous invitons à :
</p>

<ul>
    <li>Vérifier que votre demande ne relève pas d’un sujet non géré par Yellow Cactus.</li>
    <li>Remplir soigneusement tous les champs obligatoires du formulaire de demande de support avec des informations
        précises pour éviter des échanges inutiles.</li>
    <li>Grouper, si vous le pouvez, vos demandes (par tranche de 30 min).</li>
    <li>Consulter les documentations dans votre support pour les tâches que vous pouvez gérer seul.</li>
</ul>
<p>Demander conseil à votre Office Manager ou à un collègue, pour des opérations habituelles sur lesquelles vous êtes
    autonomes.</p>
<p>Les prestations sont assurées conformément au contrat de qualité de services auquel vous avez souscrit.</p>
<p>
    Vous pouvez à tout moment consulter notre page de tarification et, pour en savoir plus sur nos politiques
    d’intervention, parcourir nos conditions générales.
</p>

<p>
    * Sachez également que vous pouvez demander à notre technicien une estimation du temps de travail et des couts
    associés pour le traitement de votre requête n°{{ $ticket->id ?? 'XXXX' }}, qui, si elle dépasse les 30 min d'intervention, peut nécessiter
    des couts supérieurs à 1 ticket. Il vous répondra dans les meilleurs délais.
</p>

<p>
    Merci de votre confiance. Nous restons à votre disposition pour toute information complémentaire.
</p>

<p>Bien cordialement,<br>
    L'équipe support {{ config('app.brand_name') }}
</p>
