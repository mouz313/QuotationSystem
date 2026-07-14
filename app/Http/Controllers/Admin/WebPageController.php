<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WebPageController extends Controller
{
    public function index()
    {
        $pages = Page::latest()->get();
        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.pages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'slug'             => 'nullable|string|max:255|unique:pages',
            'content'          => 'nullable|string',
            'meta_description' => 'nullable|string|max:500',
            'is_published'     => 'sometimes|boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $validated['is_published'] = $request->boolean('is_published');

        Page::create($validated);

        return redirect('/admin/pages')->with('success', 'Page created.');
    }

    public function edit(Page $page)
    {
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, Page $page)
    {
        $validated = $request->validate([
            'title'            => 'sometimes|string|max:255',
            'slug'             => 'sometimes|string|max:255|unique:pages,slug,' . $page->id,
            'content'          => 'nullable|string',
            'meta_description' => 'nullable|string|max:500',
            'is_published'     => 'sometimes|boolean',
        ]);

        $validated['is_published'] = $request->boolean('is_published');

        $page->update($validated);

        return redirect('/admin/pages')->with('success', 'Page updated.');
    }

    public function destroy(Page $page)
    {
        $page->delete();
        return redirect('/admin/pages')->with('success', 'Page deleted.');
    }
}
