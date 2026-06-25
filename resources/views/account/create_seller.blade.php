@extends('common.index')

@section('title', 'Become a Seller - ShopPUPee')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-12">
    <div class="card w-full bg-base-100 shadow-xl border border-base-200">
        <div class="card-body">

            @if ($hasSeller)
                {{-- Already a seller --}}
                <div class="text-center py-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-success/10 text-success mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold mb-2">You're already a seller!</h2>
                    <p class="text-base-content/60 mb-6">You can manage your store from your seller dashboard.</p>
                    <a href="{{ route('dashboard.seller', Auth::user()->seller) }}" class="btn btn-primary">Go to Store Profile</a>
                </div>

            @elseif ($existingApplication && $existingApplication->status === 'Pending')
                {{-- Application pending --}}
                <div class="text-center py-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-warning/10 text-warning mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold mb-2">Application Pending Review</h2>
                    <p class="text-base-content/60">You already applied to become a seller. Please wait for an admin to review and approve your application. You'll be notified once it's been processed.</p>
                </div>

            @elseif ($existingApplication && $existingApplication->status === 'Approved')
                {{-- Approved but no seller record yet (edge case) --}}
                <div class="text-center py-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-success/10 text-success mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold mb-2">Application Approved</h2>
                    <p class="text-base-content/60">Your seller application has been approved! You can now start selling.</p>
                    <a href="{{ route('dashboard.seller', Auth::user()->seller) }}" class="btn btn-primary mt-4">Go to Store Profile</a>
                </div>

            @elseif ($existingApplication && $existingApplication->status === 'Rejected')
                {{-- Rejected — show form again --}}
                <div class="alert alert-warning mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>Your previous application was rejected. You may submit a new application below.</span>
                </div>
                @include('account._seller_form')

            @else
                {{-- No previous application — show form --}}
                @include('account._seller_form')
            @endif

        </div>
    </div>
</div>
@endsection
