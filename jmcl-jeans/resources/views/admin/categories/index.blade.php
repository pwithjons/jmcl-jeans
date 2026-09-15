@extends('admin.layouts.app')

@section('content')
<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-display font-bold text-charcoal-900">Categories</h1>
            <p class="text-charcoal-500 mt-1">Organize products into top-level categories.</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="btn-primary">+ New Category</a>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-md bg-green-50 border border-green-200 text-green-800 text-sm px-4 py-3">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-md bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3">{{ session('error') }}</div>
    @endif

    <div class="bg-white border border-charcoal-100 rounded-lg overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-charcoal-50 text-charcoal-500 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Slug</th>
                    <th class="px-4 py-3">Products</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-charcoal-100">
                @forelse ($categories as $category)
                    <tr>
                        <td class="px-4 py-3 font-medium flex items-center gap-3">
                            @if ($category->image)
                                <img src="{{ $category->image_url }}" class="w-8 h-8 rounded object-cover">
                            @endif
                            {{ $category->name }}
                        </td>
                        <td class="px-4 py-3 text-charcoal-500">{{ $category->slug }}</td>
                        <td class="px-4 py-3">{{ $category->products_count }}</td>
                        <td class="px-4 py-3">
                            <span class="{{ $category->status ? 'text-green-600' : 'text-charcoal-400' }}">
                                {{ $category->status ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right space-x-3">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="text-denim-600 hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline"
                                  onsubmit="return confirm('Delete this category?');">
                                @csrf @method('delete')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-charcoal-400">No categories yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $categories->links() }}</div>
</div>
@endsection
