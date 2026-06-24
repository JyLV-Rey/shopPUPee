@extends('common.index')
@section('title', 'Seller - Edit Profile')
@section('content')

<div class="max-w-2xl mx-auto px-4 py-10">

  @if (session('success'))
    <div class="alert alert-success mb-6">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <span>{{ session('success') }}</span>
    </div>
  @endif

    <div class="alert alert-error mb-6">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <span>{{ session('error') }}</span>
    </div>
  @endif

    <div class="alert alert-error mb-6">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <div>
        <ul class="list-disc list-inside text-sm">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    </div>
  @endif

  <div class="card bg-base-100 shadow-lg border border-base-200">
    <div class="card-body">
      <h1 class="card-title text-2xl mb-1">Edit Store</h1>
      <p class="text-base-content/60 text-sm mb-6">Update your store information below.</p>

      <form method="POST" action="{{ route('edit.seller') }}">
        @csrf

        <div class="form-control">
          <label class="label" for="seller_name">
            <span class="label-text font-medium">Store Name</span>
          </label>
          <input
            type="text"
            id="seller_name"
            name="seller_name"
            value="{{ old('seller_name', $seller->seller_name) }}"
            class="input input-bordered w-full"
            required
          />
        </div>

        <div class="card-actions justify-end mt-8">
          <a href="{{ url()->previous() }}" class="btn btn-ghost">Cancel</a>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection
