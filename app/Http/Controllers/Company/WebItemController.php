<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;

class WebItemController extends Controller
{
    public function index(Request $request)
    {
        $items = Item::where('user_id', $request->user()->id)
            ->latest()
            ->paginate(15);

        return view('company.items.index', compact('items'));
    }

    public function create()
    {
        return view('company.items.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit_price'  => 'required|numeric|min:0',
        ]);

        Item::create(array_merge($validated, ['user_id' => $request->user()->id]));

        return redirect('/items')->with('success', 'Item added.');
    }

    public function edit(Item $item)
    {
        if ($item->user_id !== request()->user()->id) abort(403);
        return view('company.items.edit', compact('item'));
    }

    public function update(Request $request, Item $item)
    {
        if ($item->user_id !== $request->user()->id) abort(403);

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit_price'  => 'required|numeric|min:0',
        ]);

        $item->update($validated);

        return redirect('/items')->with('success', 'Item updated.');
    }

    public function destroy(Item $item)
    {
        if ($item->user_id !== request()->user()->id) abort(403);
        $item->delete();
        return redirect('/items')->with('success', 'Item deleted.');
    }
}
