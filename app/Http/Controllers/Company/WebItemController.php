<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Item;
use Illuminate\Http\Request;

class WebItemController extends Controller
{
    public function index(Request $request)
    {
        $items = Item::where('user_id', $request->user()->id);

        if ($request->filled('search')) {
            $search = $request->search;
            $items->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $items = $items->latest()->paginate(15)->withQueryString();

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

        $item = Item::create(array_merge($validated, ['user_id' => $request->user()->id]));

        ActivityLog::log('item_created', $item, 'Item "' . $item->title . '" created');

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

        ActivityLog::log('item_updated', $item, 'Item "' . $item->title . '" updated');

        return redirect('/items')->with('success', 'Item updated.');
    }

    public function destroy(Item $item)
    {
        if ($item->user_id !== request()->user()->id) abort(403);
        ActivityLog::log('item_deleted', $item, 'Item "' . $item->title . '" deleted');
        $item->delete();
        return redirect('/items')->with('success', 'Item deleted.');
    }
}
