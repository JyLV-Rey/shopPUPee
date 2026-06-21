<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUser
{
    /**
     * Redirect to login if the user is not authenticated
     * or their account has been deleted.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->guest(route('account.login'));
        }

        $user = Auth::user();

        if (! $user->isActive()) {
            Auth::logout();

            return redirect()->route('account.login')->withErrors([
                'email' => 'This account has been deactivated.',
            ]);
        }

        return $next($request);
    }
}
