<?php

namespace App\Http\Middleware;

use App\Services\RoleService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CheckRole Middleware
 * 
 * Memproteksi route berdasarkan role user.
 * 
 * Contoh penggunaan:
 * - Route::middleware('role:admin') - Hanya admin
 * - Route::middleware('role:staf') - Hanya staf
 * - Route::middleware('role:admin,staf') - Admin atau staf
 */
class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $roles  Comma-separated list of allowed roles
     */
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        $user = $request->user();

        // Jika tidak ada user yang login
        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Silakan login terlebih dahulu.',
                ], 401);
            }
            return redirect()->route('login');
        }

        // Parse roles dari parameter (bisa multiple: "admin,staf")
        $allowedRoles = array_map('trim', explode(',', $roles));

        // Cek apakah user memiliki salah satu role yang diizinkan
        if (RoleService::hasAnyRole($user, $allowedRoles)) {
            return $next($request);
        }

        // User tidak memiliki akses
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk halaman ini.',
            ], 403);
        }

        abort(403, 'Anda tidak memiliki akses untuk halaman ini.');
    }
}
