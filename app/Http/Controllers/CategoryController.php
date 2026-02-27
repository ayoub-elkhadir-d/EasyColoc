<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function store(Request $request, Colocation $colocation)
    {
        if ($colocation->owner_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Category::create([
            'name' => $request->name,
            'colocation_id' => $colocation->id,
        ]);

        return back()->with('success', 'Category created');
    }

    public function destroy(Colocation $colocation, Category $category)
    {
        if ($colocation->owner_id !== auth()->id() || $category->colocation_id !== $colocation->id) {
            abort(403);
        }

        $category->delete();
        return back()->with('success', 'Category deleted');
    }
}
