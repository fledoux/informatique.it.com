@extends('layouts.public')

@section('title', __('home.Welcome'))

{{-- SEO spécifique à la homepage --}}
@section('og:type', 'website')
@section('og:title', __('home.Welcome') . ' - Expert depuis +25 ans')
@section('og:description', 'Expert en support informatique à Paris, assistance Mac/PC, réseaux, infogérance et
    cybersécurité. +25 ans d\'expérience, devis gratuit en 24h. Intervention rapide.')

@section('content')
    @include('pages._hero')
    @include('pages._solutions')
    @include('pages._offres')
    @include('pages._tarif')
    @include('pages._simulateur')
    @include('pages._teamviewer')
    @include('pages._cas')
    @include('pages._avis')
    @include('pages._faq')
    @include('pages._contact')
    @include('pages._cta')
    @include('pages._pub_yc')
        @include('pages._pub_audit')
    {{-- @include('pages._pub_goes') --}}
    {{-- @include('pages._pub_xpedit') --}}
    @include('pages.script-home')
@endsection
