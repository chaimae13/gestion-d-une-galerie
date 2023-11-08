<?php

namespace App\Http\Controllers;

use App\Models\theme;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    public function add(Request $request)
    {
        $validatedData = $request->validate([
            'nom' => 'required|string',
        ]);

        Theme::create($validatedData);

        return redirect('/gallery')->with('success', 'Theme ajouté avec succès.');
    }
    
}
