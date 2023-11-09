@extends('layout')
@section('title','home page')

    

@section('content')
    <!-- <div class="container"> -->
        <!-- <h2>Édition de la photo</h2>
        <div class="row">
            <div class="col-md-6">
                <img id="myimage" src="{{ asset('/storage/photos/' . $photo->path) }}" alt="Photo à éditer">
            </div>
            <div class="col-md-6">
                <h4>Recadrez votre photo</h4>
                <div id="cropper"></div>
                <button id="crop-btn" class="btn btn-primary">Recadrer</button>
            </div>
        </div> -->

        <div class="container">
        <h2>Édition de la photo</h2>
        <div class="row">
            <div class="col-md-6">
                <img id="myimage" src="{{ asset('/storage/photos/' . $photo->path) }}" alt="Photo à éditer">
            </div>
            <div class="col-md-6">
                <h4>Recadrez votre photo</h4>
                <div id="cropper"></div>
                <form id="crop-form" action="{{ route('photo.update', $photo->id) }}" method="POST" enctype="multipart/form-data">
    <!-- Champ d'envoi de l'image encadrée -->
    <input type="file" name="croppedImage" id="croppedImage" style="display: none;">
    <!-- Champ de données du recadrage (x, y, width, height) -->
    <input type="hidden" name="x" id="x">
    <input type="hidden" name="y" id="y">
    <input type="hidden" name="width" id="width">
    <input type="hidden" name="height" id="height">
    {{ csrf_field() }}
    <button type="submit" id="crop-btn" class="btn btn-primary">Enregistrer</button>
</form>
            </div>
        </div>
    </div>
    <!-- </div> -->

    <script>
     document.addEventListener('DOMContentLoaded', function() {
    var image = document.getElementById('myimage');
    var cropper = new Cropper(image, {
        aspectRatio: 1,
        viewMode: 3, // Mode libre pour permettre à l'utilisateur de sélectionner manuellement la zone de recadrage
    });

    document.getElementById('crop-btn').addEventListener('click', function() {
    // Obtenez les coordonnées de la zone de recadrage sélectionnée par l'utilisateur
    var cropData = cropper.getData();

    if (cropData.width <= 0 || cropData.height <= 0) {
            console.error("Width and height of cutout needs to be defined.");
            return;
        }


    // Remplissez les valeurs des champs cachés avec les coordonnées de recadrage
    document.getElementById('x').value = cropData.x;
    document.getElementById('y').value = cropData.y;
    document.getElementById('width').value = cropData.width;
    document.getElementById('height').value = cropData.height;

    // Soumettez le formulaire
    document.getElementById('crop-form').submit();
});


});
</script>

@endsection
