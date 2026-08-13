<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user()->load(['department', 'role']);
        $assignedTasks = $this->assignedTasks($user)->get();

        // Chuẩn hóa trạng thái công việc từ database
        $replacements = [
            'hoÃ n thÃ nh' => 'hoàn thành',
            'hoÃ nthÃ nh' => 'hoàn thành',
            'Ä‘ang lÃ m' => 'đang làm',
            'Ä‘anglÃ m' => 'đang làm',
            'chá»  xá»­ lÃ½' => 'chờ xử lý',
            'chá» xá»­lÃ½' => 'chờ xử lý',
            'đang lÃ m' => 'đang làm',
            'chờ xá»­ lÃ½' => 'chờ xử lý',
            'Ä‘ang review' => 'đang review',
            'quÃ¡ háº¡n' => 'quá hạn',
        ];

        $normalizeStatus = function($statusStr) use ($replacements) {
            $status = trim($statusStr ?? '');
            $status = str_replace(array_keys($replacements), array_values($replacements), $status);
            $statusLower = mb_strtolower($status);
            if (in_array($statusLower, ['done', 'hoàn thành'], true)) {
                return 'Hoàn thành';
            }
            if (in_array($statusLower, ['in progress', 'inprogress', 'in_progress', 'đang làm'], true)) {
                return 'Đang làm';
            }
            if (in_array($statusLower, ['todo', 'chờ xử lý'], true)) {
                return 'Chờ xử lý';
            }
            if (in_array($statusLower, ['đang review', 'review'], true)) {
                return 'Đang review';
            }
            if ($statusLower === 'quá hạn') {
                return 'Quá hạn';
            }
            return $status;
        };

        foreach ($assignedTasks as $task) {
            $task->status = $normalizeStatus($task->status);
        }

        $createdTasks = Task::where('assigned_by', $user->id)->count();
        $totalAssigned = $assignedTasks->count();
        $completed = $assignedTasks->where('status', 'Hoàn thành')->count();
        $inProgress = $assignedTasks->whereIn('status', ['Đang làm', 'Đang review'])->count();
        $overdue = $assignedTasks
            ->filter(fn (Task $task) => $task->deadline && $task->deadline->isPast() && $task->status !== 'Hoàn thành')
            ->count();
        $completionRate = $totalAssigned > 0 ? round(($completed / $totalAssigned) * 100) : 0;
        $dashboardRoute = $this->dashboardRoute($user);

        return view('profile.show', compact(
            'user',
            'createdTasks',
            'totalAssigned',
            'completed',
            'inProgress',
            'overdue',
            'completionRate',
            'dashboardRoute'
        ));
    }

    public function edit()
    {
        $user = Auth::user()->load(['department', 'role']);
        $dashboardRoute = $this->dashboardRoute($user);

        return view('profile.edit', compact('user', 'dashboardRoute'));
    }

    public function updateInfo(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'name.required' => 'Vui lòng nhập họ tên.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',
            'email.unique' => 'Email này đã được sử dụng.',
            'avatar.image' => 'Ảnh đại diện phải là file hình ảnh.',
            'avatar.mimes' => 'Ảnh đại diện chỉ hỗ trợ JPG, PNG hoặc WEBP.',
            'avatar.max' => 'Ảnh đại diện không được vượt quá 2MB.',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if ($request->hasFile('avatar')) {
            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
            }

            $user->avatar_path = $request->file('avatar')->store('avatars', 'public');
        }

        $user->save();

        return redirect()->route('profile.show')->with('status', 'Đã cập nhật thông tin cá nhân.');
    }

    public function editPassword()
    {
        $user = Auth::user()->load(['department', 'role']);
        $dashboardRoute = $this->dashboardRoute($user);

        return view('profile.password', compact('user', 'dashboardRoute'));
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu mới chưa khớp.',
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.'])
                ->withInput($request->except(['current_password', 'password', 'password_confirmation']));
        }

        $user->password = Hash::make($validated['password']);
        $user->save();

        return redirect()->route('profile.show')->with('status', 'Đã đổi mật khẩu thành công.');
    }

    private function assignedTasks($user)
    {
        return Task::where(function ($query) use ($user) {
            $query->where('assigned_to', $user->id)
                ->orWhereHas('assignees', fn ($assignees) => $assignees->where('users.id', $user->id));
        });
    }

    private function dashboardRoute($user): string
    {
        return match (true) {
            $user->isDirector() => route('admin.dashboard'),
            $user->isLeader() => route('manager.dashboard'),
            default => route('employee.dashboard'),
        };
    }
}
