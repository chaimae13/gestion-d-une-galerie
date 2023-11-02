<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Photo;
use Illuminate\Support\Facades\Storage;


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
    
        // Store the photo information in the database
        $user = auth()->user();
        $photoRecord = new Photo([
            'filename' => $title,
            'path' =>  $photoName,
            'user_id' => $user->id,
        ]);
        $photoRecord->save();
    
        return redirect('/gallery')->with('success', 'Photo ajoutée avec succès.');
}
public function index()
{
    // Récupérer l'utilisateur authentifié
    $user = auth()->user();

    // Charger la vue avec l'utilisateur et ses photos
    return view('photos.index', compact('user'));
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
}
