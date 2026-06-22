@extends('common.index')

@section('title', 'Create Account - ShopPUPee')

@section('content')
<div class="flex items-center justify-center min-h-[calc(100vh-100px)] px-4 py-12">
    <div class="card w-full max-w-lg bg-base-100 shadow-xl border border-base-200">
        <div class="card-body">
            <h2 class="card-title text-2xl font-bold justify-center mb-6 text-primary">Create an Account</h2>

            @if($errors->any())
                <div class="alert alert-error shadow-sm mb-4 flex-col items-start gap-1">
                    @foreach($errors->all() as $error)
                        <span class="text-sm">• {{ $error }}</span>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('account.create') }}" method="POST" class="space-y-4">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text">First Name</span>
                        </label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" placeholder="First Name" class="input input-bordered w-full" required />
                    </div>
                    
                    <div class="form-control w-full">
                        <label class="label">
                            <span class="label-text">Last Name</span>
                        </label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" placeholder="Last Name" class="input input-bordered w-full" required />
                    </div>
                </div>

                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text">Email Address</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="name@example.com" class="input input-bordered w-full" required />
                </div>
                
                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text">Phone Number</span>
                    </label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="09123456789" class="input input-bordered w-full" required />
                </div>

                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text">Password</span>
                    </label>
                    <input type="password" name="password" placeholder="Create a strong password" class="input input-bordered w-full" required />
                </div>

                <div class="form-control mt-6">
                    <button type="submit" class="btn btn-primary w-full">Create Account</button>
                </div>
            </form>

            <div class="divider text-sm text-base-content/60 mt-6">ALREADY HAVE ONE?</div>

            <div class="text-center">
                <p class="text-sm"><a href="{{ route('account.login') }}" class="link link-primary font-semibold">Log in here</a></p>
            </div>
        </div>
    </div>
</div>
@endsection
