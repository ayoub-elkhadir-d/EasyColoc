<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Depense;
use Illuminate\Http\Request;

class DepenseController extends Controller
{
    public function store(Request $request, Colocation $colocation)
    {
        $request->validate([
            'title' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        Depense::create([
            'title' => $request->title,
            'amount' => $request->amount,
            'date' => $request->date,
            'colocation_id' => $colocation->id,
            'payer_id' => auth()->id(),
            'category_id' => $request->category_id,
        ]);

        return back()->with('success', 'Expense added successfully');
    }

    public function destroy(Colocation $colocation, Depense $depense)
    {
        if ($depense->colocation_id !== $colocation->id) {
            abort(403);
        }

        $depense->delete();
        return back()->with('success', 'Expense deleted');
    }
}
