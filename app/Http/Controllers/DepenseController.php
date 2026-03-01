<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Depense;
use App\Models\Balance;
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

        $depense = Depense::create([
            'title' => $request->title,
            'amount' => $request->amount,
            'date' => $request->date,
            'colocation_id' => $colocation->id,
            'payer_id' => auth()->id(),
            'category_id' => $request->category_id,
        ]);

        $members = $colocation->members;
        if (!$members->contains($colocation->owner_id)) {
            $members->push($colocation->owner);
        }
        
        $memberCount = $members->count();
        $shareAmount = $request->amount / $memberCount;

        foreach ($members as $member) {
            Balance::create([
                'depense_id' => $depense->id,
                'user_id' => $member->id,
                'amount' => $shareAmount,
                'is_paid' => $member->id === auth()->id(),
            ]);
        }

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

    public function markPaid(Colocation $colocation, Depense $depense, Balance $balance)
    {
        if ($balance->depense_id !== $depense->id || $balance->user_id !== auth()->id()) {
            abort(403);
        }

        $balance->update(['is_paid' => true]);
        return back()->with('success', 'Marked as paid');
    }
}
