@php
    $adminRoutes = [
        ['name' => 'Buyers', 'route' => 'admin.buyers', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
        ['name' => 'Sellers', 'route' => 'admin.sellers', 'icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z'],
        ['name' => 'Orders', 'route' => 'admin.orders', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
        ['name' => 'Applications', 'route' => 'admin.applications', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        ['name' => 'Products', 'route' => 'admin.products', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
    ];
@endphp

<div class="bg-base-100 border-r border-base-200 w-full lg:w-56 shrink-0">
    <div class="p-4 border-b border-base-200">
        <h2 class="text-sm font-semibold text-base-content/60 uppercase tracking-wider">Admin Panel</h2>
    </div>
    <ul class="menu menu-sm p-2 gap-1">
        @foreach ($adminRoutes as $item)
            <li>
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors
                          {{ request()->routeIs($item['route']) ? 'bg-primary/10 text-primary font-semibold' : 'hover:bg-base-200' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}" />
                    </svg>
                    {{ $item['name'] }}
                </a>
            </li>
        @endforeach
    </ul>
</div>
