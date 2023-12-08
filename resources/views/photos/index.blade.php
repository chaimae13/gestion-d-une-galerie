@extends('layout')
@section('title', 'Gallery')
<head>
    <link rel="stylesheet" href="{{ asset('css/gallery.css') }}">
    <link rel="stylesheet" href="{{ asset('css/photos.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

</head>
@section('content')

<div class="container mt-3">
    <button class="btn btn-dark" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasWithBothOptions" aria-controls="offcanvasWithBothOptions">Add photo</button>
    <button class="btn btn-dark" type="button" onclick="toggleActions()">Toggle Actions</button>
    <a class="btn btn-dark" data-bs-toggle="offcanvas" href="#offcanvasExample" role="button" aria-controls="offcanvasExample">
        Add Theme
    </a>
    <div class="btn">
        <select class="form-select" name="Showtheme" id="Showtheme">
            <option value="" selected>View All</option>
            @foreach($themes as $theme)
                <option value="{{ $theme->id }}">{{ $theme->nom }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="offcanvas offcanvas-start  " tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasExampleLabel">Ajouter Theme</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form method="POST" action="/themes">
            @csrf
            <label class="form-label" for="nom">Ajouter Theme</label>
            <input class="form-control" type="text" name="nom" id="nom" required>
            <button class="btn btn-dark" type="submit">Ajouter</button>
        </form>
    </div>
</div>

<div class="offcanvas offcanvas-start " data-bs-scroll="true" tabindex="-1" id="offcanvasWithBothOptions" aria-labelledby="offcanvasWithBothOptionsLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasWithBothOptionsLabel">Ajouter image</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form action="/gallery" method="post" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="photo" class="form-label">Choisir des photos</label>
                <input type="file" class="form-control" name="photos[]"  multiple required>
            </div>
            <div class="mb-3">
                <select class="form-select" name="themeId" id="themeId">
                    <option class="form-label" disabled selected>Select Theme</option>
                    @foreach($themes as $theme)
                        <option value="{{ $theme->id }}">{{ $theme->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div class="dropdown-center">
                <button type="submit" class="btn btn-dark" name="addimage">Ajouter les Photos</button>
            </div>
        </form>
    </div>
</div>

<div class="container">
    <div class="box" id="photoGallery">
        <div class="dream">
        <form method="POST" action="{{ route('perform.action') }}" id="performActionForm">
            @csrf
            @foreach ($user->photos as $photo)
                <div data-theme="{{ $photo->theme_id }}">
                    <div class="photo-item">
                        <figure>
                            <div class="ml-2 actions" style="display: none;">
                                <input class="photo-checkbox" type="checkbox" name="selectedImages[]" value="{{ public_path('storage' . DIRECTORY_SEPARATOR . 'photos' . DIRECTORY_SEPARATOR . $photo->path) }}" id="checkbox{{ public_path('storage' . DIRECTORY_SEPARATOR . 'photos' . DIRECTORY_SEPARATOR . $photo->path) }}">
                                <label class="photo-label" for="checkbox{{ public_path('storage' . DIRECTORY_SEPARATOR . 'photos' . DIRECTORY_SEPARATOR . $photo->path) }}">Select</label>
                            </div>
                            <a href="{{ asset('/storage/photos/' . $photo->path) }}" target="_blank">
                                <img src="{{ asset('/storage/photos/' . $photo->path) }}" class="card-img-top" alt="Photo">
                                <div class="form-group actions" style="display: none;">
                                    <div class="form-group" style="display: flex; justify-content: center;">
                                        <a href="{{ route('photo.edit', $photo->id) }}" class="icon-link d-inline"><i class="fa fa-edit"></i></a>
                                        <a href="{{ route('getInfo', $photo->id) }}" class="icon-link d-inline"><i class="fa fa-link"></i></a>
                                        <a href="{{ route('ListerImages', $photo->id) }}" class="icon-link d-inline"><i class="fas fa-search"></i></a>
                                        <a href="{{ asset('/storage/photos/' . $photo->path) }}" download class="icon-link d-inline"><i class="fa fa-download"></i></a>
                                        <a href=""  class="delete-icon-button d-inline" onclick="deletePhoto({{ $photo->id }})" ><i class="fa fa-trash"></i></a>
                                    </div>
                                </div>
                            </a>
                        </figure>
                    </div>
                </div>
            @endforeach
        </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-dark" >Get Descriptors</button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>\
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    $(document).ready(function () {
    $('#Showtheme').change(function () {
        var selectedTheme = $(this).val();

        $('#photoGallery > div > div').each(function () {
            if (selectedTheme === '' || $(this).data('theme') == selectedTheme) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
});

    function toggleActions() {
        $('.actions').toggle();
    }

    function deletePhoto(photoId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette photo?')) {
        $.ajax({
            url: '/delete-photo/' + photoId, 
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}', 
            },
            success: function(response) {
              
                console.log(response);
                
                window.location.reload();
            },
            error: function(error) {
    
                console.error(error);
            }
        });
    }
}

    

</script>
@endsection