<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Photo;
use App\Models\theme;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;



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
    $user = auth()->user();
    $themes = theme::all();
    // $photos = Photo::all();
    // Charger la vue avec l'utilisateur et ses photos
    return view('photos.index', compact('user'), compact('themes'));
}

// public function index($themeId)
// {
//     $user = auth()->user();
//     $themes = theme::all();

//     if ($themeId == 0) {
//         $photos = $user->photos;
//     } else {
//         $photos = $user->photos->where('theme_id', $themeId);
//     }

//     return view('photos.index', compact('photos'), compact('themes'));
// }
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
    $path = '/storage/photos/' . $photo->path;

    $response = Http::post('http://127.0.0.1:5555/image', [
        'imagePath' => $filePath, // Replace with the actual image path
    ]);

    $data = $response->json();
    
    $response2 = Http::post('http://127.0.0.1:5000/ColorDominant', [
        'image_path' =>  $filePath,
    ]);

    $colors = json_decode($response2->getBody(), true)['hex_color_codes'];

    $response3 = Http::post('http://127.0.0.1:5580/momentColeur', [
        'image_path' => $filePath, // Replace with the actual image path
    ]);

    $moment = $response3->json();
    


    return view('form', ['data' => $data, 'colors' => $colors, 'moment' => $moment, 'path' => $path]);

}


}
