
@extends('layout')
@section('title', 'Gallery')
<head>
    <link rel="stylesheet" href="{{ asset('css/gallery.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
  .slideout-overlay {
background-color: rgba(215, 219, 221, 0.5);
}
</style>
</head>
@section('content')

<div class="container mt-3">
    
    <button class="btn btn-dark" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasWithBothOptions" aria-controls="offcanvasWithBothOptions">Add photo</button>
    
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
                    </form>
                        </div>
         </div>
</div>




<div class="container mt-5">
<div class="row justify-content-center mt-5" id="photoGallery">
    @foreach ($user->photos as $photo)
        <div class="col-md-4 mb-4" data-theme="{{ $photo->theme_id }}">
            <div class="card">
                <a href="{{ asset('/storage/photos/' . $photo->path) }}" target="_blank">
                    <img src="{{ asset('/storage/photos/' . $photo->path) }}" class="card-img-top" alt="Photo">
                </a>
                <div class="card-body">
                    <div class="icons text-center" style="display: flex; justify-content: center;">
                        <p>
                            <a href="{{ route('photo.edit', $photo->id) }}" class="icon-link d-inline"><i class="fa fa-edit"></i></a>
                            <a href="{{ route('getInfo', $photo->id) }}" class="icon-link d-inline"><i class="fa fa-link"></i></a>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function () {
        $('#Showtheme').change(function () {
            var selectedTheme = $(this).val();

            $('#photoGallery > div').each(function () {
                if (selectedTheme === '' || $(this).data('theme') == selectedTheme) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
    });
</script>
@endsection

