@extends('common.index')

@section('title', 'Admin — Sellers')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold tracking-tight">Manage Sellers</h1>
        <span class="badge badge-lg">{{ $sellers->total() }} total</span>
    </div>

    @if (session('success'))
        <div class="alert alert-success mb-4"><span>{{ session('success') }}</span></div>
    @endif

    <div class="overflow-x-auto rounded-xl border border-base-200 shadow-sm">
        <table class="table table-zebra w-full">
            <thead>
                <tr class="text-xs uppercase tracking-wider text-base-content/50">
                    <th>ID</th>
                    <th>Store Name</th>
                    <th>Owner</th>
                    <th>Email</th>
                    <th class="text-center">Products</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sellers as $seller)
                <tr>
                    <td class="text-xs text-base-content/50">#{{ $seller->seller_id }}</td>
                    <td class="font-medium">{{ $seller->seller_name }}</td>
                    <td>{{ $seller->buyer?->first_name }} {{ $seller->buyer?->last_name ?? '—' }}</td>
                    <td>{{ $seller->buyer?->email ?? '—' }}</td>
                    <td class="text-center">{{ $seller->products_count }}</td>
                    <td class="text-center">
                        @if($seller->is_deleted)
                            <span class="badge badge-error badge-sm">Deleted</span>
                        @else
                            <span class="badge badge-success badge-sm">Active</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <form method="POST" action="{{ route('admin.seller.toggle', $seller) }}">
                            @csrf
                            <button type="submit" class="btn btn-xs {{ $seller->is_deleted ? 'btn-success' : 'btn-error' }}">
                                {{ $seller->is_deleted ? 'Restore' : 'Disable' }}
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-8 text-base-content/50">No sellers found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $sellers->links() }}</div>
</div>
@endsection
