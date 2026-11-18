<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventBackHistory
{
    /**
     * Handle an incoming request.
     * Mencegah browser menyimpan cache halaman setelah logout
     * sehingga menekan tombol Back tidak akan menampilkan dashboard lama.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Pasang HTTP Security Headers (Anti-Clickjacking & Anti-MIME Sniffing)
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Jangan terapkan no-store pada assets (filament/assets, build/*, fonts) atau request Livewire / AJAX
        if ($request->isMethod('GET')
            && ! $request->is('livewire/*')
            && ! $request->is('filament/*')
            && ! $request->is('build/*')
            && ! $request->expectsJson()
            && ! $request->ajax()) {
            $response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', 'Sun, 02 Jan 1990 00:00:00 GMT');
        }

        return $response;
    }
}
