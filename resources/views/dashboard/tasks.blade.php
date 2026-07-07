@extends('layouts.dashboard')

@section('title', 'Công việc - MobiFone WorkHub')
@section('page_title', 'Công việc')

@section('content')
@php
    $roleVariant = $roleVariant ?? 'employee';
    $isDirector = $roleVariant === 'director';
    $isManager = $roleVariant === 'manager';
    $isEmployee = $roleVariant === 'employee';

    $page = [
        'director' => [
            'eyebrow' => 'Executive command',
            'title' => 'Điều phối mục tiêu cấp công ty',
            'desc' => 'Giao việc xuyên phòng ban, theo dõi tải công việc và ưu tiên những đầu việc ảnh hưởng toàn hệ thống.',
            'button' => 'Giao mục tiêu',
            'icon' => 'crown',
            'shell' => 'bg-[#111827] text-white',
            'accent' => '#E4002B',
            'formTitle' => 'Giao mục tiêu cấp công ty',
            'formDesc' => 'Giám đốc có thể giao việc cho bất kỳ nhân sự/phòng ban nào.',
            'submit' => 'Giao mục tiêu',
        ],
        'manager' => [
            'eyebrow' => 'Team dispatch',
            'title' => 'Giao việc trong phòng',
            'desc' => 'Tập trung phân bổ việc cho nhân viên cùng phòng, khóa phạm vi để tránh giao nhầm ngoài đội.',
            'button' => 'Giao việc cho đội',
            'icon' => 'users',
            'shell' => 'bg-white text-[#0F172A] border border-[#D7E3EA]',
            'accent' => '#0F766E',
            'formTitle' => 'Giao việc cho nhân viên',
            'formDesc' => 'Danh sách người nhận chỉ gồm nhân viên trong phòng của bạn.',
            'submit' => 'Giao việc',
        ],
        'employee' => [
            'eyebrow' => 'My workbench',
            'title' => 'Việc của tôi',
            'desc' => 'Theo dõi việc được giao và gửi đề xuất công việc cần quản lý xem xét.',
            'button' => 'Gửi đề xuất việc',
            'icon' => 'clipboard-plus',
            'shell' => 'bg-[#F0FDF4] text-[#14532D] border border-[#BBF7D0]',
            'accent' => '#16A34A',
            'formTitle' => 'Gửi đề xuất công việc',
            'formDesc' => 'Đề xuất sẽ gắn với chính bạn và ở trạng thái chờ xử lý.',
            'submit' => 'Gửi đề xuất',
        ],
    ][$roleVariant];

    $totalTasks = count($mappedTasksList);
    $doneTasks = collect($mappedTasksList)->where('status', 'Hoàn thành')->count();
    $doingTasks = collect($mappedTasksList)->where('status', 'Đang làm')->count();
    $overdueTasks = collect($mappedTasksList)->where('status', 'Quá hạn')->count();
@endphp

<div class="space-y-5">
    <section class="{{ $page['shell'] }} rounded-[8px] overflow-hidden">
        @if($isDirector)
            <div class="p-6 grid grid-cols-1 xl:grid-cols-[1.3fr_.7fr] gap-6">
                <div class="space-y-5">
                    <div class="flex items-center gap-2 text-xs uppercase tracking-widest text-white/60 font-bold">
                        <i data-lucide="{{ $page['icon'] }}" class="w-4 h-4"></i>
                        {{ $page['eyebrow'] }}
                    </div>
                    <div>
                        <h1 class="text-3xl font-black leading-tight">{{ $page['title'] }}</h1>
                        <p class="mt-2 text-sm text-white/70 max-w-2xl">{{ $page['desc'] }}</p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <button id="open-task-modal" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-[8px] text-sm font-bold text-white bg-[#E4002B] hover:bg-[#C70025] transition-colors">
                            <i data-lucide="send" class="w-4 h-4"></i>{{ $page['button'] }}
                        </button>
                        <a href="{{ route('dashboard.reports') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-[8px] text-sm font-bold text-white/80 border border-white/15 hover:bg-white/10 transition-colors">
                            <i data-lucide="bar-chart-3" class="w-4 h-4"></i>Xem báo cáo
                        </a>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-[8px] bg-white/10 p-4"><span class="text-xs text-white/50">Tổng việc</span><strong class="block text-2xl mt-1">{{ $totalTasks }}</strong></div>
                    <div class="rounded-[8px] bg-white/10 p-4"><span class="text-xs text-white/50">Đang làm</span><strong class="block text-2xl mt-1">{{ $doingTasks }}</strong></div>
                    <div class="rounded-[8px] bg-white/10 p-4"><span class="text-xs text-white/50">Hoàn thành</span><strong class="block text-2xl mt-1">{{ $doneTasks }}</strong></div>
                    <div class="rounded-[8px] bg-white/10 p-4"><span class="text-xs text-white/50">Quá hạn</span><strong class="block text-2xl mt-1 text-[#FCA5A5]">{{ $overdueTasks }}</strong></div>
                </div>
            </div>
        @elseif($isManager)
            <div class="p-6 grid grid-cols-1 xl:grid-cols-[.9fr_1.1fr] gap-5 items-center">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#CCFBF1] text-[#0F766E] text-xs font-bold uppercase tracking-wider">
                        <i data-lucide="{{ $page['icon'] }}" class="w-4 h-4"></i>{{ $page['eyebrow'] }}
                    </div>
                    <h1 class="mt-4 text-2xl font-black text-[#0F172A]">{{ $page['title'] }}</h1>
                    <p class="mt-2 text-sm text-[#64748B]">{{ $page['desc'] }}</p>
                    <button id="open-task-modal" class="mt-5 inline-flex items-center gap-2 px-4 py-2.5 rounded-[8px] text-sm font-bold text-white bg-[#0F766E] hover:bg-[#115E59] transition-colors">
                        <i data-lucide="user-check" class="w-4 h-4"></i>{{ $page['button'] }}
                    </button>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div class="rounded-[8px] bg-[#F8FAFC] border border-[#E2E8F0] p-4"><span class="text-xs text-[#64748B]">Trong phạm vi</span><strong class="block text-xl mt-1">{{ $totalTasks }}</strong></div>
                    <div class="rounded-[8px] bg-[#FFFBEB] border border-[#FDE68A] p-4"><span class="text-xs text-[#92400E]">Đang làm</span><strong class="block text-xl mt-1">{{ $doingTasks }}</strong></div>
                    <div class="rounded-[8px] bg-[#F0FDF4] border border-[#BBF7D0] p-4"><span class="text-xs text-[#166534]">Xong</span><strong class="block text-xl mt-1">{{ $doneTasks }}</strong></div>
                    <div class="rounded-[8px] bg-[#FEF2F2] border border-[#FECACA] p-4"><span class="text-xs text-[#991B1B]">Trễ hạn</span><strong class="block text-xl mt-1">{{ $overdueTasks }}</strong></div>
                </div>
            </div>
        @else
            <div class="p-5 md:p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-[8px] bg-white border border-[#BBF7D0] flex items-center justify-center">
                        <i data-lucide="{{ $page['icon'] }}" class="w-5 h-5 text-[#16A34A]"></i>
                    </div>
                    <div>
                        <div class="text-xs uppercase tracking-wider font-bold text-[#15803D]">{{ $page['eyebrow'] }}</div>
                        <h1 class="mt-1 text-2xl font-black">{{ $page['title'] }}</h1>
                        <p class="mt-1 text-sm text-[#166534]/80 max-w-2xl">{{ $page['desc'] }}</p>
                    </div>
                </div>
                <button id="open-task-modal" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-[8px] text-sm font-bold text-white bg-[#16A34A] hover:bg-[#15803D] transition-colors">
                    <i data-lucide="plus" class="w-4 h-4"></i>{{ $page['button'] }}
                </button>
            </div>
        @endif
    </section>

    <section class="flex items-center gap-3 flex-wrap justify-between">
        <div class="flex items-center gap-3 flex-wrap">
            <div class="flex rounded-[8px] border border-gray-200 bg-white overflow-hidden">
                <a href="{{ route('dashboard.tasks', ['view' => 'kanban', 'filter' => $filter, 'assignee_id' => request('assignee_id')]) }}"
                   class="flex items-center gap-1.5 px-3.5 py-2 text-sm font-semibold transition-colors {{ $viewType == 'kanban' ? 'text-white' : 'text-gray-700 hover:bg-gray-50' }}"
                   style="{{ $viewType == 'kanban' ? 'background-color: '.$page['accent'] : '' }}">
                    <i data-lucide="columns-3" class="w-3.5 h-3.5"></i> Kanban
                </a>
                <a href="{{ route('dashboard.tasks', ['view' => 'list', 'filter' => $filter, 'assignee_id' => request('assignee_id')]) }}"
                   class="flex items-center gap-1.5 px-3.5 py-2 text-sm font-semibold transition-colors {{ $viewType == 'list' ? 'text-white' : 'text-gray-700 hover:bg-gray-50' }}"
                   style="{{ $viewType == 'list' ? 'background-color: '.$page['accent'] : '' }}">
                    <i data-lucide="list" class="w-3.5 h-3.5"></i> Danh sách
                </a>
            </div>

            <div class="flex gap-2">
                @foreach(['Tất cả', 'Của tôi', 'Quá hạn'] as $f)
                    <a href="{{ route('dashboard.tasks', ['view' => $viewType, 'filter' => $f, 'assignee_id' => request('assignee_id')]) }}"
                       class="px-3.5 py-1.5 rounded-full text-xs font-semibold border transition-all {{ $filter == $f ? 'text-white' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50' }}"
                       style="{{ $filter == $f ? 'background-color: '.$page['accent'].'; border-color: '.$page['accent'] : '' }}">
                        {{ $f }}
                    </a>
                @endforeach
            </div>

            <!-- Assignee Dropdown -->
            <form action="{{ route('dashboard.tasks') }}" method="GET" class="flex items-center gap-2">
                <input type="hidden" name="view" value="{{ $viewType }}">
                <input type="hidden" name="filter" value="{{ $filter }}">
                <select name="assignee_id" onchange="this.form.submit()" class="px-3 py-1.5 rounded-xl text-xs font-semibold border border-gray-200 bg-white text-gray-700 outline-none focus:border-[#003DA5] transition-all">
                    <option value="">Lọc theo nhân viên phụ trách</option>
                    @foreach($allUsers as $u)
                        <option value="{{ $u->id }}" {{ request('assignee_id') == $u->id ? 'selected' : '' }}>
                            {{ $u->name }}
                        </option>
                    @endforeach
                </select>
                @if(request('assignee_id'))
                    <a href="{{ route('dashboard.tasks', ['view' => $viewType, 'filter' => $filter]) }}" class="text-xs text-red-500 hover:underline font-semibold flex items-center gap-1">
                        <i data-lucide="x-circle" class="w-3.5 h-3.5"></i> Xóa lọc
                    </a>
                @endif
            </form>
        </div>
    </section>

    @if($viewType == 'kanban')
        <section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            @foreach($cols as $key => $col)
                <div class="rounded-[8px] min-h-[300px] flex flex-col border border-gray-200" style="background-color: {{ $col['bg'] }};">
                    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200/70">
                        <span class="text-sm font-bold" style="color: {{ $col['color'] }}">{{ $col['label'] }}</span>
                        <span class="text-xs px-2 py-0.5 rounded-full font-bold text-white" style="background-color: {{ $col['color'] }}">{{ count($col['tasks']) }}</span>
                    </div>
                    <div class="p-3 space-y-3 flex-1 overflow-y-auto max-h-[60vh] custom-scrollbar">
                        @forelse($col['tasks'] as $task)
                            <button type="button" class="w-full text-left bg-white rounded-[8px] p-4 border border-gray-100 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200"
                                    onclick="openTaskDetail({{ json_encode($task) }})">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $task['priority'] == 'Cao' ? 'bg-red-100 text-red-700' : ($task['priority'] == 'Trung bình' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700') }}">{{ $task['priority'] }}</span>
                                    <span class="text-[10px] font-mono text-gray-400">{{ $task['code'] }}</span>
                                </div>
                                <h3 class="text-sm font-bold text-gray-800 mb-1 line-clamp-2">{{ $task['name'] }}</h3>
                                <p class="text-xs text-gray-500 mb-3 truncate">{{ $task['description'] ?: 'Chưa có mô tả chi tiết.' }}</p>
                                <div class="mb-3">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-[10px] text-gray-400">Tiến độ</span>
                                        <span class="text-[10px] font-bold" style="color: {{ $col['color'] }}">{{ $task['progress'] }}%</span>
                                    </div>
                                    <div class="h-1.5 rounded-full bg-gray-100">
                                        <div class="h-1.5 rounded-full" style="width: {{ $task['progress'] }}%; background-color: {{ $col['color'] }}"></div>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="flex items-center gap-1 text-[10px] {{ $task['status'] == 'Quá hạn' ? 'text-red-600 font-bold' : 'text-gray-400' }}">
                                        <i data-lucide="calendar" class="w-3.5 h-3.5"></i>{{ $task['deadline'] }}
                                    </span>
                                    <span class="w-6 h-6 rounded-full text-white flex items-center justify-center font-bold text-[9px]" style="background-color: {{ $page['accent'] }}" title="{{ $task['assignee'] }}">{{ $task['avatar'] }}</span>
                                </div>
                            </button>
                        @empty
                            <div class="text-center py-8 text-xs text-gray-400 border border-dashed border-gray-300 rounded-[8px] bg-white/50">Chưa có công việc.</div>
                        @endforelse

                        <button type="button" onclick="openModalForStatus('{{ $col['label'] }}')" class="w-full py-2.5 rounded-[8px] text-xs font-semibold border-2 border-dashed bg-white/60 transition-all" style="border-color: {{ $col['color'] }}33; color: {{ $col['color'] }}">
                            {{ $isEmployee ? 'Gửi đề xuất mới' : 'Thêm vào cột này' }}
                        </button>
                    </div>
                </div>
            @endforeach
        </section>
    @endif

    @if($viewType == 'list')
        <section class="bg-white rounded-[8px] border border-gray-200 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-[#F8FAFC] text-gray-500 font-semibold text-xs uppercase tracking-wide">
                            <th class="px-5 py-3">Mã</th>
                            <th class="px-5 py-3">Công việc</th>
                            <th class="px-5 py-3">Người phụ trách</th>
                            <th class="px-5 py-3">Ưu tiên</th>
                            <th class="px-5 py-3">Deadline</th>
                            <th class="px-5 py-3">Trạng thái</th>
                            <th class="px-5 py-3">Tiến độ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($mappedTasksList as $task)
                            <tr class="hover:bg-gray-50 transition-colors cursor-pointer" onclick="openTaskDetail({{ json_encode($task) }})">
                                <td class="px-5 py-4 text-xs font-mono text-gray-400">{{ $task['code'] }}</td>
                                <td class="px-5 py-4 font-bold text-gray-800 truncate max-w-[240px]">{{ $task['name'] }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="w-7 h-7 rounded-full text-white flex items-center justify-center font-bold text-[10px]" style="background-color: {{ $page['accent'] }}">{{ $task['avatar'] }}</span>
                                        <span class="text-xs text-gray-700">{{ $task['assignee'] }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4"><span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $task['priority'] == 'Cao' ? 'bg-red-100 text-red-700' : ($task['priority'] == 'Trung bình' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700') }}">{{ $task['priority'] }}</span></td>
                                <td class="px-5 py-4 text-xs font-medium {{ $task['status'] == 'Quá hạn' ? 'text-red-600 font-bold' : 'text-gray-500' }}">{{ $task['deadline'] }}</td>
                                <td class="px-5 py-4"><span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">{{ $task['status'] }}</span></td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 h-1.5 rounded-full min-w-[80px] bg-gray-100">
                                            <div class="h-1.5 rounded-full" style="width: {{ $task['progress'] }}%; background-color: {{ $page['accent'] }}"></div>
                                        </div>
                                        <span class="text-xs w-8 text-right font-mono text-gray-400">{{ $task['progress'] }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-8 text-gray-400">Không có công việc nào.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @endif
</div>

<div id="task-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 hidden">
    <div class="bg-white rounded-[8px] w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl">
        <div class="px-6 py-5 border-b border-gray-200 {{ $isDirector ? 'bg-[#111827] text-white' : '' }}">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-wider font-bold {{ $isDirector ? 'text-white/50' : 'text-gray-400' }}">{{ $page['eyebrow'] }}</p>
                    <h2 class="mt-1 text-xl font-black">{{ $page['formTitle'] }}</h2>
                    <p class="mt-1 text-sm {{ $isDirector ? 'text-white/65' : 'text-gray-500' }}">{{ $page['formDesc'] }}</p>
                </div>
                <button type="button" id="close-task-modal" class="w-8 h-8 rounded-[8px] flex items-center justify-center {{ $isDirector ? 'hover:bg-white/10' : 'hover:bg-gray-100' }}">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        </div>

        <form action="{{ route('dashboard.tasks.save') }}" method="POST" class="p-6 space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-semibold mb-1.5 text-gray-700">{{ $isEmployee ? 'Tên đề xuất *' : 'Tên công việc *' }}</label>
                <input name="task_name" required class="w-full px-4 py-3 border border-gray-200 rounded-[8px] text-sm outline-none focus:border-[color:var(--accent)]" style="--accent: {{ $page['accent'] }}" placeholder="{{ $isEmployee ? 'VD: Cần hỗ trợ rà soát hồ sơ...' : 'Nhập tên công việc...' }}" />
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1.5 text-gray-700">{{ $isEmployee ? 'Lý do / bối cảnh' : 'Mô tả' }}</label>
                <textarea name="description" rows="4" class="w-full px-4 py-3 border border-gray-200 rounded-[8px] text-sm outline-none resize-none focus:border-[color:var(--accent)]" style="--accent: {{ $page['accent'] }}" placeholder="Mô tả chi tiết..."></textarea>
            </div>

            @if(!$isEmployee)
                <div>
                    <label class="block text-sm font-semibold mb-1.5 text-gray-700">{{ $isDirector ? 'Người phụ trách *' : 'Nhân viên trong phòng *' }}</label>
                    <select name="assigned_to" required class="w-full px-4 py-3 border border-gray-200 rounded-[8px] text-sm outline-none bg-white focus:border-[color:var(--accent)]" style="--accent: {{ $page['accent'] }}">
                        <option value="">Chọn người nhận việc</option>
                        @foreach($allUsers as $u)
                            <option value="{{ $u->id }}">
                                {{ $u->name }} - {{ $u->department->TENPHONG ?? $u->department->name ?? 'Chưa có phòng' }}{{ $isDirector ? ' / '.($u->role->name ?? 'Chưa có chức vụ') : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @else
                <input type="hidden" name="assigned_to" value="{{ Auth::id() }}">
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1.5 text-gray-700">Deadline *</label>
                    <input type="date" name="deadline" required class="w-full px-4 py-3 border border-gray-200 rounded-[8px] text-sm outline-none focus:border-[color:var(--accent)]" style="--accent: {{ $page['accent'] }}" />
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5 text-gray-700">Trạng thái</label>
                    @if($isDirector)
                        <select name="status" id="task-status" class="w-full px-4 py-3 border border-gray-200 rounded-[8px] text-sm outline-none bg-white focus:border-[color:var(--accent)]" style="--accent: {{ $page['accent'] }}">
                            <option value="Chờ xử lý">Chờ xử lý</option>
                            <option value="Đang làm">Đang làm</option>
                            <option value="Đang review">Đang review</option>
                            <option value="Hoàn thành">Hoàn thành</option>
                        </select>
                    @elseif($isManager)
                        <select name="status" id="task-status" class="w-full px-4 py-3 border border-gray-200 rounded-[8px] text-sm outline-none bg-white focus:border-[color:var(--accent)]" style="--accent: {{ $page['accent'] }}">
                            <option value="Chờ xử lý">Chờ xử lý</option>
                            <option value="Đang làm">Đang làm</option>
                        </select>
                    @else
                        <input type="hidden" name="status" id="task-status" value="Chờ xử lý">
                        <div class="px-4 py-3 rounded-[8px] bg-[#F0FDF4] text-sm font-semibold text-[#15803D] border border-[#BBF7D0]">Chờ quản lý xem xét</div>
                    @endif
                </div>
            </div>

            <div class="rounded-[8px] p-4 border {{ $isDirector ? 'bg-[#F8FAFC] border-[#E2E8F0]' : ($isManager ? 'bg-[#F0FDFA] border-[#99F6E4]' : 'bg-[#F0FDF4] border-[#BBF7D0]') }}">
                <div class="flex items-start gap-3">
                    <i data-lucide="{{ $isEmployee ? 'info' : 'shield-check' }}" class="w-5 h-5 mt-0.5" style="color: {{ $page['accent'] }}"></i>
                    <div>
                        <p class="text-sm font-bold text-gray-800">{{ $isEmployee ? 'Bạn không thể giao việc cho người khác' : 'Phạm vi được kiểm soát theo chức vụ' }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $isDirector ? 'Giám đốc có phạm vi toàn công ty.' : ($isManager ? 'Quản lý chỉ giao việc cho nhân viên trong phòng.' : 'Đề xuất sẽ được tạo cho chính bạn ở trạng thái chờ xử lý.') }}</p>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-200 flex items-center justify-end gap-3">
                <button type="button" id="cancel-task-modal" class="px-5 py-2.5 rounded-[8px] text-sm font-semibold border border-gray-200 text-gray-700 hover:bg-gray-50">Hủy</button>
                <button type="submit" class="px-5 py-2.5 rounded-[8px] text-sm font-bold text-white" style="background-color: {{ $page['accent'] }}">{{ $page['submit'] }}</button>
            </div>
        </form>
    </div>
</div>

<div id="detail-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 hidden">
    <div class="bg-white rounded-[8px] w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl p-6">
        <div class="flex items-center justify-between border-b pb-4 mb-4">
            <div class="flex items-center gap-1.5 text-xs text-gray-400">
                <span>Công việc</span><i data-lucide="chevron-right" class="w-3 h-3"></i><span class="font-bold" id="detail-code" style="color: {{ $page['accent'] }}"></span>
            </div>
            <button type="button" id="close-detail-modal" class="w-8 h-8 rounded-[8px] flex items-center justify-center hover:bg-gray-100 transition-colors">
                <i data-lucide="x" class="w-5 h-5 text-gray-400"></i>
            </button>
        </div>
        <div class="space-y-4">
            <h2 class="text-2xl font-black text-[#0F172A]" id="detail-name"></h2>
            <div class="flex items-center gap-4">
                <span id="detail-priority-badge" class="px-2.5 py-0.5 rounded-full text-xs font-semibold"></span>
                <span class="text-xs text-gray-400" id="detail-deadline"></span>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-semibold mb-1">Mô tả</p>
                <p class="text-sm text-gray-700 leading-relaxed bg-gray-50 p-4 rounded-[8px]" id="detail-desc"></p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-400 font-semibold mb-1">Người phụ trách</p>
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-full text-white flex items-center justify-center font-bold text-[10px]" id="detail-avatar" style="background-color: {{ $page['accent'] }}"></span>
                        <span class="text-sm text-gray-700" id="detail-assignee"></span>
                    </div>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-semibold mb-1">Tiến độ</p>
                    <div class="text-sm font-bold" id="detail-progress" style="color: {{ $page['accent'] }}"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('task-modal');
        const openBtn = document.getElementById('open-task-modal');
        const closeBtn = document.getElementById('close-task-modal');
        const cancelBtn = document.getElementById('cancel-task-modal');
        const statusSelect = document.getElementById('task-status');

        const openModal = () => modal.classList.remove('hidden');
        const closeModal = () => modal.classList.add('hidden');

        openBtn?.addEventListener('click', openModal);
        closeBtn?.addEventListener('click', closeModal);
        cancelBtn?.addEventListener('click', closeModal);

        window.openModalForStatus = (label) => {
            if (statusSelect && statusSelect.tagName === 'SELECT') {
                if (label.includes('Đang làm')) statusSelect.value = 'Đang làm';
                else if (label.includes('Đang review') && Array.from(statusSelect.options).some(option => option.value === 'Đang review')) statusSelect.value = 'Đang review';
                else if (label.includes('Hoàn thành') && Array.from(statusSelect.options).some(option => option.value === 'Hoàn thành')) statusSelect.value = 'Hoàn thành';
                else statusSelect.value = 'Chờ xử lý';
            }
            openModal();
        };

        const detailModal = document.getElementById('detail-modal');
        const closeDetailBtn = document.getElementById('close-detail-modal');

        window.openTaskDetail = (task) => {
            document.getElementById('detail-code').innerText = task.code || ('WH-' + String(task.id).padStart(3, '0'));
            document.getElementById('detail-name').innerText = task.name;
            document.getElementById('detail-desc').innerText = task.description || 'Không có mô tả chi tiết.';
            document.getElementById('detail-deadline').innerText = 'Hạn chót: ' + task.deadline;
            document.getElementById('detail-assignee').innerText = task.assignee;
            document.getElementById('detail-avatar').innerText = task.avatar;
            document.getElementById('detail-progress').innerText = task.progress + '%';

            const badge = document.getElementById('detail-priority-badge');
            badge.innerText = task.priority;
            badge.className = 'px-2.5 py-0.5 rounded-full text-xs font-semibold';
            if (task.priority === 'Cao') badge.classList.add('bg-red-100', 'text-red-700');
            else if (task.priority === 'Trung bình') badge.classList.add('bg-amber-100', 'text-amber-700');
            else badge.classList.add('bg-blue-100', 'text-blue-700');

            detailModal.classList.remove('hidden');
        };

        closeDetailBtn?.addEventListener('click', () => detailModal.classList.add('hidden'));
    });
</script>
@endsection
