@extends('layouts.public')

@section('title', __('home.Welcome'))

{{-- SEO spécifique à la homepage --}}
@section('og:type', 'website')
@section('og:title', __('home.Welcome') . ' - Expert depuis +25 ans')
@section('og:description', 'Expert en support informatique, assistance PC/Mac, infogérance et cybersécurité. Intervention rapide, devis gratuit 24h. +25 ans d\'expérience, 9999+ interventions réussies.')

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
@include('pages._pub')
@include('pages.script-home')
@endsection