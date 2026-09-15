<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use App\Services\ProductService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(private readonly ProductService $products)
    {
    }

    public function index(Request $request): View
    {
        $products = Product::with(['category', 'subcategory', 'images'])
            ->withCount('variations')
            ->when($request->filled('search'), fn ($q) => $q->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('sku', 'like', "%{$request->search}%");
            }))
            ->when($request->filled('category'), fn ($q) => $q->where('category_id', $request->category))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::with('subcategories')->orderBy('name')->get();

        return view('admin.products.create', compact('categories'));
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $product = $this->products->create(
            data: $request->safe()->except(['variations', 'images', 'image_alt_texts', 'delete_images', 'primary_image_id']),
            variations: $request->input('variations', []),
            images: $request->file('images', []),
            imageAltTexts: $request->input('image_alt_texts', []),
        );

        return redirect()->route('admin.products.edit', $product)
            ->with('status', 'Product created successfully. You can now fine-tune variations and images below.');
    }

    public function edit(Product $product): View
    {
        $product->load(['variations', 'images']);
        $categories = Category::with('subcategories')->orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $this->products->update(
            product: $product,
            data: $request->safe()->except(['variations', 'images', 'image_alt_texts', 'delete_images', 'primary_image_id']),
            variations: $request->input('variations', []),
            images: $request->file('images', []),
            imageAltTexts: $request->input('image_alt_texts', []),
            deleteImageIds: $request->input('delete_images', []),
            primaryImageId: $request->input('primary_image_id'),
        );

        return redirect()->route('admin.products.edit', $product)->with('status', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        // Soft delete — keeps order history intact (see Product migration).
        $product->delete();

        return redirect()->route('admin.products.index')->with('status', 'Product deleted.');
    }

    public function togglePublish(Product $product): RedirectResponse
    {
        $product->update([
            'status' => $product->status === 'published' ? 'unpublished' : 'published',
        ]);

        return back()->with('status', $product->status === 'published' ? 'Product published.' : 'Product unpublished.');
    }

    /** AJAX endpoint: subcategories for a given category, used by the product form. */
    public function subcategoriesFor(Category $category)
    {
        return Subcategory::where('category_id', $category->id)
            ->orderBy('name')
            ->get(['id', 'name']);
    }
}
