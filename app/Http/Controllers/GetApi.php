<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;


class GetApi extends Controller
{
    public function getHistograms()
{
    $response = Http::post('http://127.0.0.1:5000/image', [
        'imagePath' => 'C:/Users/hp/Desktop/Cvs/chaimae_imgcv.png', // Replace with the actual image path
    ]);

    $data = $response->json();
    
    return view('form', ['data' => $data]);
    
}


// public function showColors()
//     {
//         // Make a POST request to your Flask API
//         $client = new Client();
//         $response = $client->get('http://127.0.0.1:5000/ColorDominant', [
//             'json' => ['image_path' => 'C:/Users/hp/Pictures/hey.jpeg'],
//         ]);

//         // Extract the color codes from the API response
//         $colors = json_decode($response->getBody(), true)['hex_color_codes'];
        

//         return view('form', compact('colors'));
//     }
}
