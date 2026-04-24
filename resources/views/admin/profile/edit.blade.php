@extends('layouts.admin')

@section('header', 'Profil Saya')

@section('content')
    @include('profile.partials.profile-page-content', ['user' => $user])
@endsection
