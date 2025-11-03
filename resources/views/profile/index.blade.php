@extends('layouts.template') 

@section('title', 'Profile Setting')

@section('content')
<div class="card p-4">
    <h1>Profile Setting</h1>
    <p>Ini adalah halaman pengaturan profil untuk user {{ Auth::user()->username }}.</p>
</div>
@endsection