@extends('layout')
@section('title','Welcome')
<head>
<link rel="stylesheet" href="{{ asset('css/welcome.css') }}">

</head>
@section('content')
<aside class="profile-card">
  <header>
    <!-- here’s the avatar -->
    <a target="_blank" href="#">
      <img src="{{ asset('images/2.png') }}" class="hoverZoomLink">
    </a>

    <!-- the username -->
    <h2>
    {{ $user->name }}
          </h1>

    <!-- and role or location -->
    <h4>
    {{ $user->email }}

          </h4>

  </header>

  <!-- bit of a bio; who are you? -->
  <div class="profile-bio">

    <h5> Welcome back ! </h5>
    <br>
    <a class="btn btn-dark" href="{{ route('gallery') }}">Go to Gallery </a>
    <br>


  </div>

  <!-- some social links to show off -->

</aside>

@endsection