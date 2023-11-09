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

    
        // Move the uploaded file to the storage directory
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
    // $user = auth()->user();
    $themes = theme::all();
    $photos = Photo::all();
    // Charger la vue avec l'utilisateur et ses photos
    return view('photos.index', compact('photos'), compact('themes'));
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
 
    
    // Chargez l'image d'origine
    $image = \Intervention\Image\Facades\Image::make($originalImagePath);

    // Recadrez l'image en utilisant les coordonnées fournies
    $image->crop($width, $height, $x, $y);

    // Enregistrez la nouvelle version de l'image recadrée
    $croppedImagePath = public_path('storage/photos/cropped_' . $photo->path);
    $image->save($croppedImagePath);

    // Mettez à jour le chemin de l'image dans la base de données
    $photo->path = 'cropped_' . $photo->path;
    $photo->save();

    // Supprimez l'ancienne image d'origine si nécessaire
    // File::delete($originalImagePath);

    // Redirigez l'utilisateur après l'édition de l'image
    return redirect('/gallery')->with('success', 'Photo éditée avec succès.');
}


public function delete(Photo $photo)
{
    
    $filePath = public_path('storage' . DIRECTORY_SEPARATOR . 'photos' . DIRECTORY_SEPARATOR . $photo->path);

        
    // Check if the file exists before attempting to delete
    if (file_exists($filePath)) {
        unlink($filePath); // Supprime le fichier
        $photo->delete();  // Supprime l'enregistrement de la base de données
        return redirect('/gallery')->with('success', 'Photo supprimée avec succès.');
    } else {
        dd('File not found: ' . $filePath);
    }
}


public function getHistograms(Photo $photo)
{
    $filePath = public_path('storage' . DIRECTORY_SEPARATOR . 'photos' . DIRECTORY_SEPARATOR . $photo->path);

    $response = Http::post('http://127.0.0.1:5555/image', [
        'imagePath' => $filePath, // Replace with the actual image path
    ]);

    $data = $response->json();
    
    $response2 = Http::post('http://127.0.0.1:5000/ColorDominant', [
        'image_path' =>  $filePath,
    ]);

    $colors = json_decode($response2->getBody(), true)['hex_color_codes'];

    return view('form', ['data' => $data, 'colors' => $colors]);

}


}
