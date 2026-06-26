<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\HrDocument;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user()->loadMissing(['role', 'department']);

        $roleType = 'employee';
        if ($user->isDirector()) {
            $roleType = 'director';
        } elseif ($user->isLeader()) {
            $roleType = 'leader';
        }

        $stats = [
            'users' => User::count(),
            'departments' => Department::count(),
            'tasks' => Task::count(),
            'documents' => HrDocument::count(),
            'todo' => Task::where('status', 'Todo')->count(),
            'doing' => Task::where('status', 'In Progress')->count(),
            'done' => Task::where('status', 'Done')->count(),
            'overdue' => Task::where('status', 'Overdue')->count(),
        ];

        $tasks = Task::with(['assignee', 'assigner'])
            ->when($roleType === 'employee', fn ($query) => $query->where('assigned_to', $user->id))
            ->when($roleType === 'leader', function ($query) use ($user) {
                $query->where(function ($inner) use ($user) {
                    $inner->where('assigned_by', $user->id)
                        ->orWhere('assigned_to', $user->id);
                });
            })
            ->latest()
            ->take(6)
            ->get();

        $users = User::with(['role', 'department'])->latest()->take(6)->get();
        $departments = Department::withCount('users')->orderBy('TENPHONG')->take(6)->get();

        return view('welcome', compact('user', 'roleType', 'stats', 'tasks', 'users', 'departments'));
    }
}
