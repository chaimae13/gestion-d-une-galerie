<head>
<link href="  {{asset('css/header.css')}}" rel="stylesheet">
</head>
<body>
  <nav class="navbar navbar-light" style="background-color: #DAC6C8;">
    <a href="{{ route('gallery') }}">
      <img src="{{ asset('images/gallery.png') }}" class="logo" alt="Gallery">
    </a>
    <a  class="nav-item" href="{{ route('gallery') }}">Gallery</a> <!-- Link to the Gallery route -->
    <a  class="nav-item" href="{{ route('login') }}">Login</a>
    <a  class="nav-item" href="{{ route('register') }}">Register</a>
  </nav>
</body>

