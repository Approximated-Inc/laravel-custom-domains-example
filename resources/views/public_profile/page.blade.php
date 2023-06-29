@extends('layouts.public_profile_layout')
@section('main')
    <div class="max-w-7xl mx-auto sm:p-6 lg:p-8 space-y-6 mt-24 bg-white shadow sm:rounded-lg">
        <div class="text-xl">This should only load on {{ $user->custom_domain }}</div>
        <div class="text-xl">The user's name is {{ $user->name }}</div>
        <div class="text-xl">This is page {{ $page }}</div>
    </div>
@endsection