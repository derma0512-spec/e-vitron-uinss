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
