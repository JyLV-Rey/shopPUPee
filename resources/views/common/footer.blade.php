<footer class="footer sm:footer-horizontal bg-neutral text-neutral-content p-10">
  <aside>
    <div class="bg-primary rounded-lg p-2 w-fit">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary-content" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
      </svg>
    </div>
    <p class="font-bold text-lg">shopPUPee</p>
    <p class="text-sm text-neutral-content/60">A multi-vendor marketplace for campus sellers.</p>
    <p class="text-xs text-neutral-content/40">Built with Laravel, Tailwind &amp; DaisyUI</p>
  </aside>

  <nav>
    <h6 class="footer-title">Navigate</h6>
    <a href="{{ route('home') }}" class="link link-hover text-sm">Home</a>
    <a href="{{ route('search') }}" class="link link-hover text-sm">Browse Products</a>
    <a href="{{ route('account.login') }}" class="link link-hover text-sm">Sign In</a>
  </nav>

  <nav>
    <h6 class="footer-title">Support</h6>
    <a href="{{ route('home') }}" class="link link-hover text-sm">FAQ</a>
    <a href="{{ route('home') }}" class="link link-hover text-sm">Terms of Service</a>
    <a href="{{ route('home') }}" class="link link-hover text-sm">Privacy Policy</a>
  </nav>

  <nav>
    <h6 class="footer-title">Connect</h6>
    <div class="grid grid-flow-col gap-4">
      <a href="https://github.com/JyLV-Rey/shopPUPee" target="_blank" rel="noopener noreferrer" title="GitHub">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="fill-current hover:opacity-70 transition-opacity">
          <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
        </svg>
      </a>
    </div>
  </nav>
</footer>
