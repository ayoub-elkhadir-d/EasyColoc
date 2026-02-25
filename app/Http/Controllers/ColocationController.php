<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use Illuminate\Http\Request;

class ColocationController extends Controller
{
    public function index()
    {
        $colocations = Colocation::all();
        return view('home', compact('colocations'));
    }

    public function Create(Request $request)
    {
        $request->validate([
            'num' => 'required|string',
            'description' => 'required|string',
        ]);
         
        Colocation::create([
            
            'num' => $request->num,
            'description' => $request->description,
        ]);

        return redirect()->route('homeColoc')->with('success', 'Colocation add');
         
    }
}
