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
                @csrf
                <div>
                    <label for="title">Nom de la photo</label>
                    <input type="text" name="title">
                </div>
                <div>
                    <label for="photo">Choisir une photo</label>
                    <input type="file" name="photo" required>
                </div>
                <button type="submit" name="addimage">Ajouter la Photo</button>
            </form>
        </div>
    </div>
    
    <!-- Affichage des Photos de l'Utilisateur -->
    <div class="show-images">
        <!-- <h2>Mes Photos</h2> -->
        @foreach ($user->photos as $photo)
            <div class="gallerie">
                <div class="gallerie_image">
                    <img src="{{ asset('/storage/photos/' . $photo->path) }}" alt="Photo">
                    <div class="icons">
                        <p>
                        <a href=""><i class="fa fa-edit"></i></a>
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
@endsection