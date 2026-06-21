<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    /**
     * Abort with 403 if the authenticated user is not an admin.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('account.login')->withErrors([
                'email' => 'This account has been deactivated.',
            ]);
        }

        /** @var \App\Models\Buyer $user */
        $user = Auth::user();

        if (! $user->isAdmin()) {
            abort(403, 'Unauthorized — admin access only.');
        }

        return $next($request);
    }
}
