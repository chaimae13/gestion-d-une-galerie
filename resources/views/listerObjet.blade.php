@extends('layout')
@section('title', 'Gallery')
<!DOCTYPE html>
<html lang="en">

<head>
    <style>
        .dream {
            column-width: 320px;
            column-gap: 15px;
            width: 90%;
            max-width: 1100px;
            margin: 50px auto;
        }

        .head {
            margin-top: 10px;
            
        }

        .dream figure {
            background: #fefefe;
            box-shadow: 0 1px 2px rgba(34, 25, 25, 0.4);
            margin: 0 2px 15px;
            padding: 5px;
            padding-bottom: 10px;
            transition: opacity .4s ease-in-out;
            display: inline-block;
        }

        .dream .form-group {
            display: none;
        }

        .dream figure img {
            width: 100%;
            height: auto;
           
        }

        
    </style>
</head>
@section('content')
    <div class="container">
    <h1 class="mb-4">Photos Similaires</h1>
        <div class="box">
            <div class="dream" style="display: flex; justify-content: center;">
                <!-- Image de requête ici -->
                <figure>
                    <img src="{{ asset('/storage/photos/' . $queryPhotoPath) }}" class="card-img-top" alt="Query image">
                </figure>
            </div>
        </div>

        <div class="dream">
            @foreach ($photos as $photo)
                <figure>
                    <img src="{{ asset('/storage/photos/' . $photo->path) }}" class="card-img-top" alt="Photo Similaire">
                    <!-- Ajoutez ici si vous voulez des boutons ou d'autres éléments pour chaque photo similaire -->
                </figure>
            @endforeach
        </div>
    </div>
@endsection
