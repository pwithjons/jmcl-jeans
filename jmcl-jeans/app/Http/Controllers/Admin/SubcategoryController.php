<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SubcategoryRequest;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class SubcategoryController extends Controller
{
    public function index(): View
    {
        $subcategories = Subcategory::with('category')
            ->withCount('products')
            ->orderBy('sort_order')
            ->paginate(20);

        return view('admin.subcategories.index', compact('subcategories'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.subcategories.create', compact('categories'));
    }

    public function store(SubcategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $data['status'] = $request->boolean('status', true);

        Subcategory::create($data);

        return redirect()->route('admin.subcategories.index')->with('status', 'Subcategory created successfully.');
    }

    public function edit(Subcategory $subcategory): View
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.subcategories.edit', compact('subcategory', 'categories'));
    }

    public function update(SubcategoryRequest $request, Subcategory $subcategory): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $data['status'] = $request->boolean('status');

        $subcategory->update($data);

        return redirect()->route('admin.subcategories.index')->with('status', 'Subcategory updated successfully.');
    }

    public function destroy(Subcategory $subcategory): RedirectResponse
    {
        if ($subcategory->products()->exists()) {
            return back()->with('error', 'Cannot delete a subcategory that still has products assigned to it.');
        }

        $subcategory->delete();

        return redirect()->route('admin.subcategories.index')->with('status', 'Subcategory deleted.');
    }
}
