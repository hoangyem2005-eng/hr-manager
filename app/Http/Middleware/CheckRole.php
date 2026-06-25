<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string|int  ...$roles
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Nếu không truyền roles cụ thể, mặc định yêu cầu quyền Trưởng phòng (role_id = 1)
        if (empty($roles)) {
            $roles = [1];
        }

        foreach ($roles as $role) {
            // Kiểm tra theo ID vai trò (VD: 1, 2, 3)
            if (is_numeric($role) && $user->role_id == $role) {
                return $next($request);
            }
            
            // Kiểm tra theo tên vai trò (VD: 'Admin', 'Quản lý', 'Nhân viên')
            if (is_string($role) && $user->role && strtolower($user->role->name) == strtolower($role)) {
                return $next($request);
            }

            // Hỗ trợ bí danh tiếng Việt "Trưởng phòng" tương ứng với role_id = 1
            if ($role === 'Trưởng phòng' && $user->role_id == 1) {
                return $next($request);
            }
        }

        // Nếu không thuộc danh sách quyền được phép, chặn truy cập
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Bạn không có quyền truy cập chức năng này.'], 403);
        }

        return redirect()->route('dashboard.index')->with('error', 'Bạn không có quyền truy cập chức năng này. Chỉ dành cho Trưởng phòng.');
    }
}
