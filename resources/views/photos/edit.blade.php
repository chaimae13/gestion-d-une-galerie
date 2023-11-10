@extends('layout')
@section('title','home page')

    
<head>
    <style>
#myimage {
    max-width: 100%; 
    height: auto; /* Permet à l'image de conserver son ratio hauteur-largeur */
}

/* Style de la fenêtre modale */
.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgb(0,0,0);
    background-color: rgba(0,0,0,0.4);
    padding-top: 60px;
    
}

.modal-content {
    background-color: #fefefe;
    margin: 5% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

.mycolor{
   color: #0e233bb0;
}

    </style>

</head>
    

@section('content')
    <div class="container">
        <h2 class="mt-3 mycolor">Édition de la photo</h2>
        <div class="row">
            <div class="col-md-6">
                <img id="myimage" src="{{ asset('/storage/photos/' . $photo->path) }}" alt="Photo à éditer">
            </div>
            <div class="col-md-6">
                <h4 class="mycolor">Options d'édition</h4>
                <!-- Boutons -->
                <button id="crop-btn" class="btn btn-dark">Crop</button>
                <button id="change-scale-btn" class="btn btn-dark">Change Scale</button>
                <!-- <button  class="btn btn-primary">Changer en Gris</button> -->
                <!-- Formulaire de recadrage -->
                <form id="crop-form" action="{{ route('photo.update', $photo->id) }}" method="POST" enctype="multipart/form-data">
                    <!-- Champ d'envoi de l'image encadrée -->
                    <input type="file" name="croppedImage" id="croppedImage" style="display: none;">
                    <!-- Champ de données du recadrage (x, y, width, height) -->
                    <input type="hidden" name="x" id="x">
                    <input type="hidden" name="y" id="y">
                    <input type="hidden" name="width" id="width">
                    <input type="hidden" name="height" id="height">
                    {{ csrf_field() }}
                    <button type="submit" id="enre" class="btn btn-dark" style=" position: absolute; bottom:10vh;">Enregistrer</button>
                </form>
            </div>
        </div>
    </div>

    <div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Changer l'échelle de l'image</h2>
        <form id="scale-form" action="{{ route('photo.changeScale', $photo->id) }}" method="POST">
            @csrf
            <label for="scale-factor">Facteur d'échelle :</label>
            <input type="number" step="0.1" id="scale-factor" name="scaleFactor" required>
            <button type="submit" class="btn btn-primary">Appliquer</button>
        </form>
    </div>
    </div>




    <script>
     document.addEventListener('DOMContentLoaded', function() {

   

    document.getElementById('crop-btn').addEventListener('click', function() {

        var image = document.getElementById('myimage');
    var cropper = new Cropper(image, {
        aspectRatio: 1,
        viewMode: 3, // Mode libre pour permettre à l'utilisateur de sélectionner manuellement la zone de recadrage
    });

    document.getElementById('enre').addEventListener('click', function() {
    // Obtenir les coordonnées de la zone de recadrage sélectionnée par l'utilisateur
    var cropData = cropper.getData();

    if (cropData.width <= 0 || cropData.height <= 0) {
            console.error("Width and height of cutout needs to be defined.");
            return;
        }

    document.getElementById('x').value = cropData.x;
    document.getElementById('y').value = cropData.y;
    document.getElementById('width').value = cropData.width;
    document.getElementById('height').value = cropData.height;

    document.getElementById('crop-form').submit();
});
  

});

var modal = document.getElementById('myModal');


var btn = document.getElementById('change-scale-btn');

var span = document.getElementsByClassName('close')[0];


btn.onclick = function() {
    modal.style.display = 'block';
}

span.onclick = function() {
    modal.style.display = 'none';
}

// Lorsque l'utilisateur clique en dehors de la fenêtre modale, on ferme
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}

});
</script>

@endsection
