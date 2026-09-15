@extends('admin.layouts.app')

@section('content')
<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-display font-bold text-charcoal-900">Subcategories</h1>
            <p class="text-charcoal-500 mt-1">Group products more finely within a category.</p>
        </div>
        <a href="{{ route('admin.subcategories.create') }}" class="btn-primary">+ New Subcategory</a>
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
                    <th class="px-4 py-3">Category</th>
                    <th class="px-4 py-3">Products</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-charcoal-100">
                @forelse ($subcategories as $subcategory)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $subcategory->name }}</td>
                        <td class="px-4 py-3 text-charcoal-500">{{ $subcategory->category->name }}</td>
                        <td class="px-4 py-3">{{ $subcategory->products_count }}</td>
                        <td class="px-4 py-3">
                            <span class="{{ $subcategory->status ? 'text-green-600' : 'text-charcoal-400' }}">
                                {{ $subcategory->status ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right space-x-3">
                            <a href="{{ route('admin.subcategories.edit', $subcategory) }}" class="text-denim-600 hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.subcategories.destroy', $subcategory) }}" class="inline"
                                  onsubmit="return confirm('Delete this subcategory?');">
                                @csrf @method('delete')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-charcoal-400">No subcategories yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $subcategories->links() }}</div>
</div>
@endsection
