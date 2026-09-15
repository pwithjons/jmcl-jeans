@extends('admin.layouts.app')

@section('content')
<div>
    <h1 class="text-2xl font-display font-bold text-charcoal-900">Admin Accounts</h1>
    <p class="text-charcoal-500 mt-1">Visible to Super Admins only. Create/deactivate actions arrive in Phase 3.</p>

    <div class="mt-6 bg-white border border-charcoal-100 rounded-lg overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-charcoal-50 text-charcoal-500 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Role</th>
                    <th class="px-4 py-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-charcoal-100">
                @foreach ($admins as $admin)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $admin->name }}</td>
                        <td class="px-4 py-3 text-charcoal-500">{{ $admin->email }}</td>
                        <td class="px-4 py-3">{{ $admin->role === 'super_admin' ? 'Super Admin' : 'Admin' }}</td>
                        <td class="px-4 py-3">
                            <span class="{{ $admin->is_active ? 'text-green-600' : 'text-red-600' }}">
                                {{ $admin->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
