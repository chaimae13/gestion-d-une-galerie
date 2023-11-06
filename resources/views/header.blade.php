<head>
<link href="  {{asset('css/header.css')}}" rel="stylesheet">
</head>
<body>
<header class="header">
        <img src="{{asset('images/logoBu2.png')}}" class="logo"></img>
        <nav class="navbar">
            <a href="#">Home</a>
            <a href="#">Gallery</a>
            <a href="#">Contact</a>
            <a href="{{route('login')}}">login</a>
            <a href="{{route('register')}}">Register</a>
        </nav>
      </header>
</body>