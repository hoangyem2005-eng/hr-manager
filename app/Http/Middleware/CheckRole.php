<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (empty($roles)) {
            $roles = [User::ROLE_MANAGER];
        }

        foreach ($roles as $role) {
            if (is_numeric($role) && (int) $user->role_id === (int) $role) {
                return $next($request);
            }

            if (is_string($role) && $user->role && mb_strtolower($user->role->name) === mb_strtolower($role)) {
                return $next($request);
            }

            $aliases = [
                'Giám đốc' => User::ROLE_ADMIN,
                'Trưởng phòng' => User::ROLE_MANAGER,
                'Nhân viên' => User::ROLE_EMPLOYEE,
            ];

            if (isset($aliases[$role]) && (int) $user->role_id === $aliases[$role]) {
                return $next($request);
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Bạn không có quyền truy cập chức năng này.'], 403);
        }

        return redirect()
            ->route('dashboard.index')
            ->with('error', 'Bạn không có quyền truy cập chức năng này.');
    }
}
