<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <link href="  {{asset('css/login.css')}}" rel="stylesheet">
    </head>
  
    <body>
    <div class="body">
        <div class="login">
        <h1 style="color: white; letter-spacing: 0.3em;" >login</h1>
        <img src="{{asset('images/logo1.jpg')}}"  >
        <form  action="{{route('login.post')}}" method="POST" class="login-form">
            @csrf
             <input type="text" placeholder="Enter email " id="email" name="email" >
             <input type="password" placeholder="Enter votre password" id="password" name="password">
             <button type="submit" >LOGIN</button>
        </form>
    </div>
    </div>  
    </body>
</html>