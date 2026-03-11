<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckBlockedUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Check if user is blocked
            if ($user->isBlocked()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                $blockType = $user->block_type === 'permanent' ? 'permanen' : 'sampai ' . $user->blocked_until->format('d/m/Y H:i');
                
                return redirect()->route('login')->withErrors([
                    'email' => "Akun Anda telah diblokir secara {$blockType}. Silakan hubungi administrator.",
                ]);
            }
        }
        
        return $next($request);
    }
}

