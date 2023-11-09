@extends('layout')
@section('title','home page')
<head>
    <link rel="stylesheet" href="{{asset('css/gallery.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    

</head>
@section('content')
<div class="container">
        <div class="add-images">
            <h1>Ajouter une image</h1>
            <form action="/gallery" method="post" enctype="multipart/form-data">
            <div style="display: flex; margin:20px ;">
                @csrf
                <div>
                    <label for="title">Nom de la photo</label>
                    <input type="text" name="title">
                </div>
                <div>
                <option value disabled selected>--Select Theme --</option>
                 <select name="themeId" id="themeId">
                 @foreach($themes as $theme)
                     <option value="{{ $theme->id }}">{{ $theme->nom }}</option>
                 @endforeach
                 </select>
                 </div>
                <div>
                    <label for="photo">Choisir une photo</label>
                    <input type="file" name="photo" required>
                </div>
            </div>
                <button type="submit" name="addimage">Ajouter la Photo</button>

        
   
    
        <!-- Ajouter Theme -->
                
    </form>           
            <form method="POST" action="/themes">
             @csrf
            <label for="nom">Ajouter Theme</label>
            <input type="text" name="nom" id="nom" required>
            <button type="submit">Ajouter</button>
        </form>
        </div>
 </div>
<div style="background-color:gray;"></div>
                 <select name="Showtheme" id="Showtheme">
                 <option value disabled selected>--Select Theme --</option>

                 @foreach($themes as $theme)
                     <option value="{{ $theme->id }}">{{ $theme->nom }}</option>
                 @endforeach
                 </select>
                 </div>
    <!-- Affichage des Photos de l'Utilisateur -->
    <div class="show-images">
        <!-- <h2>Mes Photos</h2> -->
        @foreach ($photos as $photo)
            <div class="gallerie">
            <div class="gallerie theme-{{ $photo->themeId }}">
                <div class="gallerie_image">
                    <img src="{{ asset('/storage/photos/' . $photo->path) }}" alt="Photo">
                    <div class="icons">
                        <p>
                        <a href="{{ route('photo.edit', $photo->id) }}"><i class="fa fa-edit"></i></a>
                        <a href="{{ route('getHistograms', $photo->id ) }}"><i class="fa fa-link"></i></a>
                        <a href="{{ asset('/storage/photos/' . $photo->path) }}" download><i class="fa fa-download"></i></a>
                        
                        <form action="{{ route('photo.delete', $photo->id) }}" method="post" class="delete-form">
                         @csrf
                         @method('DELETE')
                        <button type="submit" class="delete-icon-button"><i class="fa fa-trash"></i></button>
                        </form>
                     
                        </p>
                    </div>
                   
                </div>
            </div>
         @endforeach
            
     
    </div>
    </div>

    <script>
    // Wrap the JavaScript code in a DOMContentLoaded event listener
    document.addEventListener('DOMContentLoaded', function() {
        const showThemeSelect = document.getElementById('Showtheme');

        showThemeSelect.addEventListener('change', function () {
            const selectedThemeId = this.value;
            const allImages = document.querySelectorAll('.gallerie');

            if (selectedThemeId === '') {
                allImages.forEach(image => {
                    image.style.display = 'block';
                });
            } else {
                allImages.forEach(image => {
                    image.style.display = 'none';
                });

                const selectedThemeImages = document.querySelectorAll('.theme-' + selectedThemeId);
                selectedThemeImages.forEach(image => {
                    image.style.display = 'block';
                });
            }
        });
    });
</script>
@endsection