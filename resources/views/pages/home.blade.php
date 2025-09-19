@extends('layouts.public')

@section('title', __('Home.Welcome'))

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
@include('pages.script-home')
@endsection