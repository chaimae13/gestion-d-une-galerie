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
    public function getHistograms(Photo $photo)
    {
        $filePath = public_path('storage' . DIRECTORY_SEPARATOR . 'photos' . DIRECTORY_SEPARATOR . $photo->path);
        $path = '/storage/photos/' . $photo->path;

        $response = Http::post('http://127.0.0.1:5555/image', [
            'imagePath' => $filePath,
        ]);

        $data = $response->json();

        $response2 = Http::post('http://127.0.0.1:5500/ColorDominant', [
            'image_path' => $filePath,
        ]);

        $colors = json_decode($response2->getBody(), true)['hex_color_codes'];

        $response3 = Http::post('http://127.0.0.1:5580/momentColeur', [
            'image_path' => $filePath,
        ]);

        $moment = $response3->json();


        //  array with the data
        $jsonData = [
            'data' => $data,
            'colors' => $colors,
            'moment' => $moment,
            'path' => $path,
        ];

        // Convert the array to JSON format
        $jsonContent = json_encode($jsonData, JSON_PRETTY_PRINT);

        // Store the JSON content in a file
        $jsonFileName = 'photo_data_' . $photo->id . '.json';
        Storage::put('json_data/' . $jsonFileName, $jsonContent);

    }

    public function viewJSON($photo_id)
    {

        $filename = 'json_data/photo_data_' . $photo_id . '.json';

        // Vérifier si le fichier existe dans le répertoire storage
        if (Storage::exists($filename)) {
            // Lire le contenu du fichier
            $content = Storage::get($filename);

            $jsonData = json_decode($content, true);

            // Vérifier si le décodage a réussi
            if ($jsonData === null) {
                return response()->json(['error' => 'Erreur de décodage JSON'], 500);
            }
            $data = $jsonData['data'];
            $colors = $jsonData['colors'];

            $moment = $jsonData['moment'];
            $path = $jsonData['path'];
            $paths[] = $path;




            return view('form', ['data' => $data, 'colors' => $colors, 'moment' => $moment, 'path' => $paths[0]]);
        } else {

            return response()->json(['error' => 'Fichier non trouvé'], 404);
        }


    }


    public function performAction(Request $request)
    {

        $selectedImages = $request->input('selectedImages', []);
        // dd($selectedImages);
        // Make asynchronous requests to the three APIs
        $responses = Http::withOptions(['verify' => false])->post('http://127.0.0.1:5555/image', ['selectedImages' => $selectedImages]);
        $data = $responses->json();
        $colors = Http::withOptions(['verify' => false])->post('http://127.0.0.1:5500/ColorDominant', ['selectedImages' => $selectedImages])->json();

        $moment = Http::withOptions(['verify' => false])->post('http://127.0.0.1:5580/momentColeur', ['selectedImages' => $selectedImages])->json();

        $jsonData = [
            'data' => $data,
            'colors' => $colors,
            'moment' => $moment,
            'path' => $selectedImages,
        ];

        // Convert the array to JSON format
        $jsonContent = json_encode($jsonData, JSON_PRETTY_PRINT);

        // Store the JSON content in a file
        $jsonFileName = 'photo_try_.json';
        Storage::put('json_data/' . $jsonFileName, $jsonContent);

        // Handle each API response and update the UI
        return view('form', ['data' => $data, 'colors' => $colors["hex_color_codes"], 'moment' => $moment, 'path' => $selectedImages]);
    }

    public function upload(Request $request)
    {
        $request->validate([

            'themeId' => 'required',
        ]);



        // Process each uploaded file

        $user = auth()->user();
        foreach ($request->file('photos') as $photo) {
            $photoName = $photo->getClientOriginalName();

            $photo->storeAs('photos', $photoName, 'public');

            $title = pathinfo($photoName, PATHINFO_FILENAME);
            $themeId = $request->input('themeId');



            // Store the photo information in the database
            $photoRecord = new Photo([
                'filename' => $title,
                'path' => $photoName,
                'user_id' => $user->id,
                'theme_id' => $themeId,
            ]);

            $photoRecord->save();
            $this->getHistograms($photoRecord);

        }


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
        // Récupérer la photo à partir de l'ID
        $photo = Photo::find($id);

        // Récupérer les coordonnées de recadrage de la requête
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
        // $photo->path = 'cropped_' . $photo->path;
        // $photo->save();

        $newPhoto = new Photo();
        $newPhoto->path = 'cropped_' . $photo->path;
        $newPhoto->filename = $photo->filename;
        $newPhoto->user_id = $photo->user_id;
        $newPhoto->save();

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
        $scaledImage = $image->resize($image->width() * $scaleFactor, $image->height() * $scaleFactor);

        // Enregistrer la nouvelle version de l'image  dans le storage
        $scaledImagePath = public_path('storage/photos/scaled_' . $photo->path);
        $scaledImage->save($scaledImagePath);


        // Créer un nouvel enregistrement dans la base de données pour la version échelonnée
        $newPhoto = new Photo();
        $newPhoto->path = 'scaled_' . $photo->path;
        $newPhoto->filename = $photo->filename;
        $newPhoto->user_id = $photo->user_id;
        $newPhoto->save();

        return redirect('/gallery')->with('success', 'Photo éditée avec succès.');
    }
    // public function delete($photoId)
    // {
    //     $photo = Photo::find($photoId);
    //     $filePath = public_path('storage' . DIRECTORY_SEPARATOR . 'photos' . DIRECTORY_SEPARATOR . $photo->path);


    //     // Check if the file exists before delete
    //     if (file_exists($filePath)) {
    //         unlink($filePath); // Supprimer le fichier
    //         $photo->delete();  // Supprimer l'enregistrement de la base de données
    //         // return redirect('/gallery')->with('success', 'Photo supprimée avec succès.');
    //     } else {
    //         dd('File not found: ' . $filePath);
    //     }
    // }
    public function fetchDataFromFlaskApi($photo_id)
    {
        // Find the photo by ID
        $photo = Photo::findOrFail($photo_id);

        // Get the path and name of the selected image
        $imagePath = $photo->path;
        $imageName = pathinfo($imagePath, PATHINFO_FILENAME);

        // Define the path for saving JSON files
        $jsonFolderPath = 'json';
        $jsonFilePath = $jsonFolderPath . '/' . $imageName . '.json';

        // Check if the JSON file exists
        if (!Storage::exists($jsonFilePath)) {
            // If the file doesn't exist, make a request to Flask API
            $apiUrl = 'http://127.0.0.1:5550/api/getImageDistance';

            $datasetPath = public_path('storage' . DIRECTORY_SEPARATOR . 'photos');

            $response = Http::post($apiUrl, [
                'original_image' => $imagePath,
                'folder' => $datasetPath,
            ]);

            $similarImages = $response->json()['result'];

            // Save the JSON response to a file
            Storage::disk('public')->put($jsonFilePath, json_encode($similarImages));
        } else {
            // If the file exists, retrieve data from the file
            $similarImages = json_decode(Storage::disk('public')->get($jsonFilePath), true);
        }

        // Get the image names
        $imageNames = array_column($similarImages, 0);

        // Get the top 10 image names
        $topImageNames = array_slice($imageNames, 0, 10);

        // Pass the data to the view
        return view('ListerImages', compact('topImageNames', 'photo'));
    }
    public function submitFeedback(Request $request)
    {
        // Get the topImageNames and photo_id from the request
        $topImageNames = json_decode($request->input('topImageNames'), true); // Note the 'true' argument

        // Ensure $topImageNames is an array
        $topImageNames = is_array($topImageNames) ? $topImageNames : [];

        $photo_id = $request->input('photo_id');

        // Process feedback data
        $feedbackData = $request->input('feedback');

        // Ensure $feedbackData is an array
        $feedbackData = is_array($feedbackData) ? $feedbackData : [];

        // Prepare feedback data in a format suitable for your API
        $formattedFeedback = [];
        foreach ($feedbackData as $index => $feedback) {
            // Check if $topImageNames[$index] is set before using it
            if (isset($topImageNames[$index])) {
                $formattedFeedback[] = [
                    'filename' => $topImageNames[$index],
                    'relevance' => $feedback,
                ];
            }
        }

        // Submit feedback to your Flask API
        $apiUrl = 'http://127.0.0.1:5550/api/feedback';
        $response = Http::post($apiUrl, [
            'feedback' => $formattedFeedback,
        ]);

        // Handle the response as needed

        // Redirect back to the image search results
        return redirect()->route('/gallery')->with('success', 'Feedback submitted successfully.');

    }

}