@extends('layout')
@section('title', 'home page')
<head>
    <link rel="stylesheet" href="{{ asset('css/gallery.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8" >
            <div class="card" style="background-color:#F9F6F5">
                <div class="card-title mt-3">
                    <h1 class="text-center">Ajouter une image</h1>
                </div>
                <div class="card-body">
                    <form action="/gallery" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="title" class="form-label">Nom de la photo</label>
                            <input type="text" class="form-control" name="title">
                        </div>
                        <div class="mb-3">
                            <label for="photo" class="form-label">Choisir une photo</label>
                            <input type="file" class="form-control" name="photo" required>
                        </div>
                        <button type="submit" class="btn btn-dark" name="addimage">Ajouter la Photo</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center mt-4">
        @foreach ($user->photos as $photo)
        <div class="col-md-4 mb-4">
            <div class="card">
            <a href="{{ asset('/storage/photos/' . $photo->path) }}" target="_blank">
                <img src="{{ asset('/storage/photos/' . $photo->path) }}" class="card-img-top" alt="Photo">
                </a>
                <div class="card-body">
                    <div class="icons text-center">
                    <p>
    <a href="" class="icon-link d-inline"><i class="fa fa-edit"></i></a>
    <a href="{{ route('getHistograms', $photo->id) }}" class="icon-link d-inline"><i class="fa fa-link"></i></a>
    <a href="{{ asset('/storage/photos/' . $photo->path) }}" download class="icon-link d-inline"><i class="fa fa-download"></i></a>
    <form action="{{ route('photo.delete', $photo->id) }}" method="post" class="delete-form d-inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="delete-icon-button d-inline"><i class="fa fa-trash"></i></button>
    </form>
</p>


                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
