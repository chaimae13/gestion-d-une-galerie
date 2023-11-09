<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'Laravel Project')</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <link href="  {{asset('css/login.css')}}" rel="stylesheet">
    <title>Register</title>
    <link rel="shortcut icon" href="{{ asset('images/camera.png') }}">


    </head>
<div class="body container">
        <div class="mt-5">
            @if($errors->any())
            <div class="col-4 float-left">
                @foreach($errors->all() as $error)
                <div class="alert alert-primary">
                    {{$error}}
                </div>
                @endforeach
            </div>
            @endif

            @if(session()->has('success'))
            <div class="alert alert-danger">{{session('success')}}</div>
            @endif

            
            @if(session()->has('error'))
            <div class="alert alert-danger">{{session('error')}}</div>
            @endif
        </div>
        <div class="login">
        <h1 style="color: white; letter-spacing: 0.2em;" >S'inscrire</h1>
        <form action="{{route('register.post')}}" method="post" class="login-form">
        @csrf
             <input type="text" placeholder="Nom " id="name" name="name" >
             <input type="text" placeholder="Email" id="email" name="email">
             <input type="password" placeholder="Enter votre password" id="password" name="password">
             <input type="password" placeholder="Repeter votre password" id="Verpassword" name="Verpassword">
             <button type="submit" >Register</button>
        </form>
    </div>
    </div>  
    </body>
</html>