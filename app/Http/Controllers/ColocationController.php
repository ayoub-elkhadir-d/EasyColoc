<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use Illuminate\Http\Request;

class ColocationController extends Controller
{
    public function index()
    {
        $colocation = auth()->user()->ownedColocations()->first() ;
        return view('home', compact('colocation'));
    }

    public function Create(Request $request)
    {
        if (auth()->user()->hasActiveMembership()) {
            return back()->withErrors(['error' => 'You already have an active colocation']);
        }

        $request->validate([
            'num' => 'required|string',
            'description' => 'required|string',
        ]);
         
        Colocation::create([
            'num' => $request->num,
            'description' => $request->description,
            'owner_id' => auth()->id(),
        ]);

        return redirect()->route('home')->with('success', 'Colocation created');
    }

    public function update(Request $request, Colocation $colocation)
    {
        // if ($colocation->owner_id !== auth()->id()) {
        //     abort(403);
        // }

        // $request->validate([
        //     'num' => 'required|string',
        //     'description' => 'required|string',
        // ]);

        // $colocation->update($request->only(['num', 'description']));
        // return redirect()->route('home')->with('success', 'Colocation updated');
    }

    public function destroy(Colocation $colocation)
    {
        if ($colocation->owner_id !== auth()->id()) {
            abort(403);
        }

        $colocation->delete();
        return redirect()->route('home')->with('success', 'Colocation deleted');
    }
    
    public function leave(Colocation $colocation)
    {
        auth()->user()->colocations()->detach($colocation->id);
        return redirect()->route('home')->with('success', 'You left the colocation');
    }

    public function removeMember(Colocation $colocation, $userId)
    {
        if ($colocation->owner_id !== auth()->id()) {
            abort(403);
        }

        $colocation->members()->detach($userId);
        return back()->with('success', 'Member removed');
    }
}
