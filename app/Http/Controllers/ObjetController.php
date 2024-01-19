<?php

namespace App\Http\Controllers;

use App\Models\photo;
use App\Models\theme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ObjetController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([

            'themeId' => 'required',
        ]);

        // Process each uploaded file

        $user = auth()->user();
        foreach ($request->file('photos') as $photo) {
            $photoName = $photo->getClientOriginalName();
            $photoNameWithoutExtension = pathinfo($photoName, PATHINFO_FILENAME);
    $modelFileName = $photoNameWithoutExtension . '.obj';
    $modelFilePath = 'C:/Users/hp/Desktop/CBIR/ccbir/tesss/dataset/3D_Models/Abstract/' . $modelFileName;



            if ($this->photoExists($photoName)) {
                continue; // Skip adding the photo and move to the next iteration
            }

            $photo->storeAs('photos', $photoName, 'public');

            $title = pathinfo($photoName, PATHINFO_FILENAME);
            // $title = $request->input('title') ?? $photoName;
            // $title = $photoName;         
            $themeId = $request->input('themeId');



            // Store the photo information in the database
            $photoRecord = new photo([
                'filename' => $title,
                'path' => $photoName,
                'user_id' => $user->id,
                'theme_id' => $themeId,
            ]);

            $photoRecord->save();


            $response = Http::post('http://127.0.0.1:5000/upload_model', [
                'filename' => $modelFilePath,
            ]);
            
            if ($response->successful()) {
                $apiResponse = $response->json();

            } else {
                 [
                    'status' => $response->status(),
                    'error' => $response->body(),
                ];
            }

            // You can handle the API response as needed
            $apiResponse = $response->json();
        }
        return redirect('/objet')->with('success', 'Photo ajoutée avec succès.');
    }



    private function photoExists($photoName)
{
    // Check if a photo with the same filename already exists in the database
    return Photo::where('path', $photoName)->exists();
}
public function delete($photoId)
    {

        $photo = Photo::find($photoId);
        $filePath = public_path('storage' . DIRECTORY_SEPARATOR . 'photos' . DIRECTORY_SEPARATOR . $photo->path);
    
    
        // Check if the file exists before delete
        if (file_exists($filePath)) {
            unlink($filePath); // Supprimer le fichier
            $photo->delete();  // Supprimer l'enregistrement de la base de données
            return redirect('/objet')->with('success', 'Photo supprimée avec succès.');
            // return redirect('/gallery')->with('success', 'Photo supprimée avec succès.');
        } else {
            dd('File not found: ' . $filePath);
        }
    }


    public function index(Request $request)
    {
        $user = auth()->user();
        $themes = theme::all();
        $perPage = 12;
        $currentPage = $request->input('page', 1);
        $selectedTheme = $request->input('theme'); // Get selected theme ID from query params
    
        // Fetch photos based on selected theme or all photos if no theme selected
        $photosQuery = DB::table('photos')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc');
    
        if ($selectedTheme) {
            $photosQuery->where('theme_id', $selectedTheme);
        }
    
        $photos = $photosQuery->paginate($perPage);
    
        return view('Objet', compact('user', 'themes', 'photos', 'selectedTheme'));
    }

    public function listSimilarObjects($id) {
        // Récupérer l'objet Photo par son ID
        $photo = Photo::find($id);
        if (!$photo) {
            abort(404, 'Photo non trouvée');
        }
    
        // Construire le chemin du fichier JSON
        $jsonFilePath = 'C:/Users/hp/Desktop/CBIR/ccbir/tesss/save_models/' . $photo->filename . '.json';
    
        // Appeler l'API pour obtenir des objets similaires
        $response = Http::post('http://127.0.0.1:5000/search_similar', [
            'file_path' => $jsonFilePath
        ]);
    
        if ($response->failed()) {
            abort(500, 'Erreur de serveur API');
        }
    
        $similarModelNames = $response->json()['similar_models'];
    
        // Préparer les détails des modèles similaires
        $similarPhotos = [];
        foreach ($similarModelNames as $modelName) {
            // Retirer l'extension .json pour obtenir le nom de la photo
            $photoName = basename($modelName, '.json');
    
            // Rechercher la photo dans le stockage
            $similarPhoto = Photo::where('filename', $photoName)->first();
            if ($similarPhoto) {
                array_push($similarPhotos, $similarPhoto);
            }
        }
    
        // Afficher la vue avec les photos similaires
        return view('listerObjet', [ 'queryPhotoPath' => $photo->path,'photos' => $similarPhotos]);
    }
}    