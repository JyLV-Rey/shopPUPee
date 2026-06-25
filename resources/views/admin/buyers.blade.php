@extends('common.index')

@section('title', 'Admin — Buyers')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold tracking-tight">Manage Buyers</h1>
        <span class="badge badge-lg">{{ $buyers->total() }} total</span>
    </div>

    @if (session('success'))
        <div class="alert alert-success mb-4"><span>{{ session('success') }}</span></div>
    @endif
    @if (session('error'))
        <div class="alert alert-error mb-4"><span>{{ session('error') }}</span></div>
    @endif

    <div class="overflow-x-auto rounded-xl border border-base-200 shadow-sm">
        <table class="table table-zebra w-full">
            <thead>
                <tr class="text-xs uppercase tracking-wider text-base-content/50">
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Joined</th>
                    <th class="text-center">Orders</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($buyers as $buyer)
                <tr>
                    <td class="text-xs text-base-content/50">#{{ $buyer->buyer_id }}</td>
                    <td class="font-medium">{{ $buyer->first_name }} {{ $buyer->last_name }}</td>
                    <td>{{ $buyer->email }}</td>
                    <td>{{ $buyer->phone ?? '—' }}</td>
                    <td class="text-sm">{{ $buyer->created_at ? \Carbon\Carbon::parse($buyer->created_at)->format('M d, Y') : '—' }}</td>
                    <td class="text-center">{{ $buyer->orders_count }}</td>
                    <td class="text-center">
                        @if($buyer->is_deleted)
                            <span class="badge badge-error badge-sm">Deleted</span>
                        @else
                            <span class="badge badge-success badge-sm">Active</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <form method="POST" action="{{ route('admin.buyer.toggle', $buyer) }}">
                            @csrf
                            <button type="submit" class="btn btn-xs {{ $buyer->is_deleted ? 'btn-success' : 'btn-error' }}">
                                {{ $buyer->is_deleted ? 'Restore' : 'Disable' }}
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-8 text-base-content/50">No buyers found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $buyers->links() }}</div>
</div>
@endsection
