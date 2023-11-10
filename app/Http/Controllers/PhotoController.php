<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Photo;
use App\Models\theme;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Image;


class PhotoController extends Controller
{
   
    public function __construct()
{
    $this->middleware('auth');
}
    public function upload(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        $photo = $request->file('photo');
        $photoName = $photo->getClientOriginalName();

        $photo->storeAs('photos', $photoName, 'public'); // This stores the file in the "storage/app/public/photos" directory

        $title = $request->input('title') ?? $photoName;
        $themeId= $request->input('themeId');
    
        // Store the photo information in the database
        $user = auth()->user();
        $photoRecord = new Photo([
            'filename' => $title,
            'path' =>  $photoName,
            'user_id' => $user->id,
            'theme_id'=>$themeId,
        ]);
        $photoRecord->save();
    
        return redirect('/gallery')->with('success', 'Photo ajoutée avec succès.');
}

public function index()
{
    // Récupérer l'utilisateur authentifié
    $user = auth()->user();
    $themes = theme::all();

    return view('photos.index', compact('user'), compact('themes'));
}


public function edit($id)
{
    $photo = Photo::find($id);
    return view('photos.edit', compact('photo'));
}


public function update(Request $request, $id)
{
    // Récupérez la photo à partir de l'ID
    $photo = Photo::find($id);

    // Récupérez les coordonnées de recadrage de la requête
    $x = intval($request->input('x'));
    $y = intval($request->input('y'));
    $width = intval($request->input('width'));
    $height = intval($request->input('height'));

    if ($width <= 0 || $height <= 0) {
        return redirect('/gallery')->with("error", "Width and height of cutout needs to be defined.");
    }

    // Chemin vers l'image d'origine
    $originalImagePath = public_path('storage/photos/' . $photo->path);
 
    
    // Charger l'image d'origine
    $image = \Intervention\Image\Facades\Image::make($originalImagePath);

    // Recadrer l'image 
    $image->crop($width, $height, $x, $y);

    // Enregistrer la nouvelle version de l'image recadrée
    $croppedImagePath = public_path('storage/photos/cropped_' . $photo->path);
    $image->save($croppedImagePath);

    // Mettre à jour le chemin de l'image dans la base de données
    $photo->path = 'cropped_' . $photo->path;
    $photo->save();

    // Supprimer l'ancienne image d'origine 
    // File::delete($originalImagePath);

    // Rediriger l'utilisateur après l'édition de l'image
    return redirect('/gallery')->with('success', 'Photo éditée avec succès.');
}

public function changeScale(Request $request, $id)
    {
        $photo = Photo::find($id);
        // Valider les données du formulaire
        $request->validate([
            'scaleFactor' => 'required|numeric',
        ]);

         // Chemin vers l'image d'origine
    $originalImagePath = public_path('storage/photos/' . $photo->path);
 
    
      // Charger l'image d'origine
      $image = \Intervention\Image\Facades\Image::make($originalImagePath);


        // Récupérer le facteur d'échelle du formulaire
        $scaleFactor = $request->input('scaleFactor');

        // Appliquer le changement d'échelle
        $scaledImage =  $image->resize($image->width() * $scaleFactor, $image->height() * $scaleFactor);

         // Enregistrer la nouvelle version de l'image  dans le storage
    $scaledImagePath = public_path('storage/photos/scaled_' . $photo->path);
    $scaledImage->save($scaledImagePath);
    

    // Créer un nouvel enregistrement dans la base de données pour la version échelonnée
    $newPhoto = new Photo();
    $newPhoto->path = 'scaled_'. $photo->path;
    $newPhoto->filename = $photo->filename;
    $newPhoto->user_id = $photo->user_id ;
    $newPhoto->save();
       
    return redirect('/gallery')->with('success', 'Photo éditée avec succès.');
    }
public function delete(Photo $photo)
{
    
    $filePath = public_path('storage' . DIRECTORY_SEPARATOR . 'photos' . DIRECTORY_SEPARATOR . $photo->path);

        
    // Check if the file exists before delete
    if (file_exists($filePath)) {
        unlink($filePath); // Supprimer le fichier
        $photo->delete();  // Supprimer l'enregistrement de la base de données
        return redirect('/gallery')->with('success', 'Photo supprimée avec succès.');
    } else {
        dd('File not found: ' . $filePath);
    }
}


public function getHistograms(Photo $photo)
{
    $filePath = public_path('storage' . DIRECTORY_SEPARATOR . 'photos' . DIRECTORY_SEPARATOR . $photo->path);
    $path = '/storage/photos/' . $photo->path;

    $response = Http::post('http://127.0.0.1:5555/image', [
        'imagePath' => $filePath, 
    ]);

    $data = $response->json();
    
    $response2 = Http::post('http://127.0.0.1:5000/ColorDominant', [
        'image_path' =>  $filePath,
    ]);

    $colors = json_decode($response2->getBody(), true)['hex_color_codes'];

    $response3 = Http::post('http://127.0.0.1:5580/momentColeur', [
        'image_path' => $filePath, 
    ]);

    $moment = $response3->json();
    


    return view('form', ['data' => $data, 'colors' => $colors, 'moment' => $moment, 'path' => $path]);

}


}