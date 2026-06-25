@extends('common.index')

@section('title', 'Admin — Seller Applications')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold tracking-tight">Seller Applications</h1>
        <span class="badge badge-lg">{{ $applications->total() }} total</span>
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
                    <th>Applicant</th>
                    <th>Store Name</th>
                    <th>Date</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $app)
                <tr>
                    <td class="text-xs text-base-content/50">#{{ $app->application_id }}</td>
                    <td>
                        <p class="font-medium text-sm">{{ $app->buyer?->first_name }} {{ $app->buyer?->last_name }}</p>
                        <p class="text-xs text-base-content/50">{{ $app->buyer?->email }}</p>
                    </td>
                    <td class="font-medium">{{ $app->seller_name }}</td>
                    <td class="text-sm">{{ $app->application_date ? \Carbon\Carbon::parse($app->application_date)->format('M d, Y') : '—' }}</td>
                    <td class="text-center">
                        @if($app->status === 'Pending')
                            <span class="badge badge-warning badge-sm">Pending</span>
                        @elseif($app->status === 'Approved')
                            <span class="badge badge-success badge-sm">Approved</span>
                        @else
                            <span class="badge badge-error badge-sm">Rejected</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($app->status === 'Pending')
                            <div class="flex gap-1 justify-center">
                                <form method="POST" action="{{ route('admin.application.approve', $app) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-xs btn-success"
                                            onclick="return confirm('Approve this seller application?')">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('admin.application.reject', $app) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-xs btn-error"
                                            onclick="return confirm('Reject this seller application?')">Reject</button>
                                </form>
                            </div>
                        @else
                            <span class="text-xs text-base-content/40">Processed</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-8 text-base-content/50">No applications found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $applications->links() }}</div>
</div>
@endsection
