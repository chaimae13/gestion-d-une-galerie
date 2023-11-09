<head>
<link href="  {{asset('css/header.css')}}" rel="stylesheet">

</head>
<body>
  <nav class="navbar navbar-expand-sm navbar-light" style="background-color: #DAC6C8;">
    <a href="{{ route('gallery') }}">
      <img src="{{ asset('images/gallery.png') }}" class="logo" alt="Gallery" style="width: 60px">
    </a>
    <a  class="nav-link" href="{{ route('gallery') }}">Gallery</a> <!-- Link to the Gallery route -->
    <a  class="nav-item" href="{{ route('login') }}">Login</a>
    <a  class="nav-item" href="{{ route('register') }}">Register</a>
    <a  class="nav-item" href="{{ route('logout') }}">Logout</a>
  </nav>
</body>

