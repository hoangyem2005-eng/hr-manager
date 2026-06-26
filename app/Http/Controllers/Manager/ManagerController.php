<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Task;
use App\Models\Department;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ManagerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            if (!$user || !$user->isLeader()) {
                // Nếu là Giám đốc, redirect sang admin dashboard
                if ($user && $user->isDirector()) {
                    return redirect()->route('admin.dashboard');
                }
                return redirect()->route('employee.dashboard')
                    ->with('error', 'Trang này chỉ dành cho Trưởng phòng.');
            }
            return $next($request);
        });
    }

    public function dashboard()
    {
        $user       = Auth::user();
        $deptId     = $user->department_id;

        // Chỉ lấy nhân viên trong phòng mình
        $myTeam = User::with('role')
            ->where('department_id', $deptId)
            ->where('id', '!=', $user->id) // bỏ bản thân
            ->get()
            ->map(function ($u) {
                $total  = Task::where('assigned_to', $u->id)->count();
                $done   = Task::where('assigned_to', $u->id)->where('status', 'Hoàn thành')->count();
                $doing  = Task::where('assigned_to', $u->id)->where('status', 'Đang làm')->count();
                $overdue = Task::where('assigned_to', $u->id)->where('status', 'Quá hạn')->count();
                return [
                    'id'         => $u->id,
                    'name'       => $u->name,
                    'email'      => $u->email,
                    'role_name'  => $u->role->name ?? 'Nhân viên',
                    'avatar'     => $this->getInitials($u->name),
                    'total'      => $total,
                    'done'       => $done,
                    'doing'      => $doing,
                    'overdue'    => $overdue,
                    'rate'       => $total > 0 ? round(($done / $total) * 100) : 0,
                ];
            });

        // Công việc trong phòng mình đã giao (assigned_by = tôi)
        $myAssignedTasks = Task::with('assignee')
            ->where('assigned_by', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(fn($t) => [
                'id'       => $t->id,
                'code'     => 'WH-' . str_pad($t->id, 3, '0', STR_PAD_LEFT),
                'name'     => $t->task_name,
                'assignee' => $t->assignee->name ?? '—',
                'status'   => $t->status,
                'deadline' => $t->deadline ? Carbon::parse($t->deadline)->format('d/m/Y') : '—',
                'progress' => $t->progress ?? 0,
            ]);

        // Công việc được giao cho nhân viên trong phòng
        $teamMemberIds = User::where('department_id', $deptId)->pluck('id');
        $pendingCount  = Task::whereIn('assigned_to', $teamMemberIds)->where('status', 'Chờ xử lý')->count();
        $doingCount    = Task::whereIn('assigned_to', $teamMemberIds)->where('status', 'Đang làm')->count();
        $doneCount     = Task::whereIn('assigned_to', $teamMemberIds)->where('status', 'Hoàn thành')->count();
        $overdueCount  = Task::whereIn('assigned_to', $teamMemberIds)->where('status', 'Quá hạn')->count();

        $department = $user->department;
        $allTeamMembers = User::where('department_id', $deptId)->get(); // cho form giao việc
        $roles = Role::all();

        return view('manager.dashboard', compact(
            'myTeam', 'myAssignedTasks',
            'pendingCount', 'doingCount', 'doneCount', 'overdueCount',
            'department', 'allTeamMembers', 'roles'
        ));
    }

    /**
     * Thêm nhân viên mới – department_id bị LOCK theo phòng của Trưởng phòng.
     */
    public function storeEmployee(Request $request)
    {
        $manager = Auth::user();

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'role_id'       => User::ROLE_EMPLOYEE,    // Luôn là Nhân viên
            'department_id' => $manager->department_id, // Bị lock theo phòng của Trưởng phòng
        ]);

        return redirect()->route('manager.dashboard')
            ->with('success', "Đã thêm nhân viên {$request->name} vào phòng của bạn!");
    }

    /**
     * Giao công việc cho cấp dưới trong phòng.
     */
    public function assignTask(Request $request)
    {
        $manager = Auth::user();
        $deptId  = $manager->department_id;

        $request->validate([
            'task_name'   => 'required|string|max:255',
            'assigned_to' => 'required|exists:users,id',
            'deadline'    => 'required|date',
            'description' => 'nullable|string',
            'status'      => 'nullable|string',
        ]);

        // Verify: người được giao phải trong phòng của mình
        $assignee = User::findOrFail($request->assigned_to);
        if ($assignee->department_id !== $deptId) {
            return back()->with('error', 'Bạn chỉ có thể giao việc cho nhân viên trong phòng của mình!');
        }

        Task::create([
            'task_name'   => $request->task_name,
            'description' => $request->description,
            'assigned_by' => $manager->id,
            'assigned_to' => $request->assigned_to,
            'deadline'    => $request->deadline,
            'status'      => $request->status ?? 'Chờ xử lý',
            'progress'    => 0,
        ]);

        return redirect()->route('manager.dashboard')
            ->with('success', "Đã giao công việc cho {$assignee->name}!");
    }

    // ===================== Helper =====================
    private function getInitials(string $name): string
    {
        $words = explode(' ', $name);
        if (count($words) >= 2) {
            return mb_strtoupper(
                mb_substr($words[count($words) - 2], 0, 1) .
                mb_substr($words[count($words) - 1], 0, 1)
            );
        }
        return mb_strtoupper(mb_substr($name, 0, 2));
    }
}
