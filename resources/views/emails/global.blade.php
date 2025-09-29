<style>
    html,
    body {
        margin: 0;
        padding: 20px;
        background-color: #f8f9fa;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    }
</style>
<div
    style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border: 1px solid #dee2e6; border-radius: 0.375rem; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); padding: 1.5rem; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
    <img src="{{ $message->embed(public_path('assets/img/logo/logo-horizontal.svg')) }}" alt="informatique.it.com"
        style="width:240px; margin-bottom: 60px !important;">
    <h1 style="color: #212529; font-size: 2rem; font-weight: bold; margin-bottom: 1rem; margin-top: 0;">
        {{ $title ?? 'Hello' }}
    </h1>
    <p style="color: #6c757d; font-size: 1rem; line-height: 1.5; margin-bottom: 0;">
        Votre message : {{ $content ?? 'Aucun message fourni.' }}
    </p>
</div>

<div
    style="max-width: 600px; margin: 0 auto; padding: 1.5rem; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
    @include('emails._baseline')
</div>
