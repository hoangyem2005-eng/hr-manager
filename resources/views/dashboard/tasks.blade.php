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
            'shell' => 'mf-director-task-hero text-white',
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
            'shell' => 'mf-manager-task-hero text-[#071325]',
            'accent' => '#003DA5',
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
            'shell' => 'mf-employee-task-hero text-[#071325]',
            'accent' => '#003DA5',
            'formTitle' => 'Gửi đề xuất công việc',
            'formDesc' => 'Đề xuất sẽ gắn với chính bạn và ở trạng thái chờ xử lý.',
            'submit' => 'Gửi đề xuất',
        ],
    ][$roleVariant];

    $totalTasks = count($mappedTasksList);
    $doneTasks = collect($mappedTasksList)->where('status', 'Hoàn thành')->count();
    $doingTasks = collect($mappedTasksList)->where('status', 'Đang làm')->count();
    $overdueTasks = collect($mappedTasksList)->where('status', 'Quá hạn')->count();
    $managerCols = [
        'pending' => ['color' => '#334155', 'bg' => '#F8FAFC', 'line' => '#CBD5E1'],
        'doing' => ['color' => '#003DA5', 'bg' => '#F4F8FF', 'line' => '#B9CDF5'],
        'review' => ['color' => '#0057C8', 'bg' => '#EEF5FF', 'line' => '#93BDF8'],
        'done' => ['color' => '#001F5B', 'bg' => '#F7FAFF', 'line' => '#003DA5'],
    ];
    $employeeCols = [
        'pending' => ['color' => '#64748B', 'bg' => '#F8FAFC', 'line' => '#CBD5E1'],
        'doing' => ['color' => '#003DA5', 'bg' => '#F4F8FF', 'line' => '#B9CDF5'],
        'review' => ['color' => '#0057C8', 'bg' => '#EEF5FF', 'line' => '#93BDF8'],
        'done' => ['color' => '#001F5B', 'bg' => '#F7FAFF', 'line' => '#003DA5'],
    ];
    $taskIndexRoute = $isEmployee ? 'employee.tasks' : 'dashboard.tasks';
    $taskSaveRoute = $isEmployee ? 'employee.tasks.save' : 'dashboard.tasks.save';
@endphp

<div class="space-y-5">
    <section class="{{ $page['shell'] }} rounded-[8px] overflow-hidden">
        @if($isDirector)
            <div class="p-6 grid grid-cols-1 xl:grid-cols-[1.3fr_.7fr] gap-6">
                <div class="space-y-5">
                    <div class="flex items-center gap-2 text-xs uppercase tracking-widest text-white/70 font-bold">
                        <i data-lucide="{{ $page['icon'] }}" class="w-4 h-4"></i>
                        {{ $page['eyebrow'] }}
                    </div>
                    <div>
                        <h1 class="text-3xl font-black leading-tight">{{ $page['title'] }}</h1>
                        <p class="mt-2 text-sm text-white/80 max-w-2xl">{{ $page['desc'] }}</p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <button id="open-task-modal" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-[8px] text-sm font-bold text-white bg-[#E4002B] hover:bg-[#C70025] transition-colors">
                            <i data-lucide="send" class="w-4 h-4"></i>{{ $page['button'] }}
                        </button>
                        <a href="{{ route('dashboard.reports') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-[8px] text-sm font-bold text-white border border-white/25 bg-white/10 hover:bg-white/15 transition-colors">
                            <i data-lucide="bar-chart-3" class="w-4 h-4"></i>Xem báo cáo
                        </a>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="mf-director-stat-card rounded-[8px] p-4"><span class="text-xs text-white/70">Tổng việc</span><strong class="block text-2xl mt-1">{{ $totalTasks }}</strong></div>
                    <div class="mf-director-stat-card rounded-[8px] p-4"><span class="text-xs text-white/70">Đang làm</span><strong class="block text-2xl mt-1">{{ $doingTasks }}</strong></div>
                    <div class="mf-director-stat-card rounded-[8px] p-4"><span class="text-xs text-white/70">Hoàn thành</span><strong class="block text-2xl mt-1">{{ $doneTasks }}</strong></div>
                    <div class="mf-director-stat-card rounded-[8px] p-4"><span class="text-xs text-white/70">Quá hạn</span><strong class="block text-2xl mt-1 text-[#FFD0D8]">{{ $overdueTasks }}</strong></div>
                </div>
            </div>
        @elseif($isManager)
            <div class="p-6 grid grid-cols-1 xl:grid-cols-[.9fr_1.1fr] gap-5 items-center">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white text-[#003DA5] border border-[#B9CDF5] text-xs font-bold uppercase tracking-wider shadow-sm">
                        <i data-lucide="{{ $page['icon'] }}" class="w-4 h-4"></i>{{ $page['eyebrow'] }}
                    </div>
                    <h1 class="mt-4 text-3xl font-black text-[#001F5B] tracking-tight">{{ $page['title'] }}</h1>
                    <p class="mt-2 text-sm leading-6 text-[#40516B] max-w-2xl">{{ $page['desc'] }}</p>
                    <button id="open-task-modal" class="mt-5 inline-flex items-center gap-2 px-4 py-2.5 rounded-[8px] text-sm font-bold text-white bg-[#003DA5] hover:bg-[#0057C8] transition-colors shadow-[0_12px_24px_rgba(0,61,165,.18)]">
                        <i data-lucide="user-check" class="w-4 h-4"></i>{{ $page['button'] }}
                    </button>
                    <button id="open-proposal-modal" class="mt-5 inline-flex items-center gap-2 px-4 py-2.5 rounded-[8px] text-sm font-bold text-[#003DA5] bg-[#E8F0FE] hover:bg-[#D4E3FC] transition-colors shadow-sm border border-[#B9CDF5] ml-2">
                        <i data-lucide="send" class="w-4 h-4"></i>Gửi đề xuất việc
                    </button>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div class="mf-manager-stat-card p-4"><span class="text-xs text-[#64748B]">Trong phạm vi</span><strong class="block text-2xl mt-1 text-[#001F5B]">{{ $totalTasks }}</strong></div>
                    <div class="mf-manager-stat-card p-4"><span class="text-xs text-[#64748B]">Đang làm</span><strong class="block text-2xl mt-1 text-[#003DA5]">{{ $doingTasks }}</strong></div>
                    <div class="mf-manager-stat-card p-4"><span class="text-xs text-[#64748B]">Xong</span><strong class="block text-2xl mt-1 text-[#001F5B]">{{ $doneTasks }}</strong></div>
                    <div class="mf-manager-stat-card mf-manager-stat-danger p-4"><span class="text-xs text-[#64748B]">Trễ hạn</span><strong class="block text-2xl mt-1 text-[#E4002B]">{{ $overdueTasks }}</strong></div>
                </div>
            </div>
        @else
            <div class="p-5 md:p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-[8px] bg-white border border-[#B9CDF5] flex items-center justify-center shadow-sm">
                        <i data-lucide="{{ $page['icon'] }}" class="w-5 h-5 text-[#003DA5]"></i>
                    </div>
                    <div>
                        <div class="text-xs uppercase tracking-wider font-bold text-[#003DA5]">{{ $page['eyebrow'] }}</div>
                        <h1 class="mt-1 text-3xl font-black text-[#001F5B] tracking-tight">{{ $page['title'] }}</h1>
                        <p class="mt-1 text-sm text-[#40516B] max-w-2xl leading-6">{{ $page['desc'] }}</p>
                    </div>
                </div>
                <button id="open-task-modal" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-[8px] text-sm font-bold text-white bg-[#003DA5] hover:bg-[#0057C8] transition-colors shadow-[0_12px_24px_rgba(0,61,165,.18)]">
                    <i data-lucide="plus" class="w-4 h-4"></i>{{ $page['button'] }}
                </button>
            </div>
        @endif
    </section>

    <section class="flex items-center gap-3 flex-wrap justify-between">
        <div class="flex items-center gap-3 flex-wrap">
            <div class="flex rounded-[8px] border border-gray-200 bg-white overflow-hidden">
                <a href="{{ route($taskIndexRoute, ['view' => 'kanban', 'filter' => $filter, 'assignee_id' => request('assignee_id')]) }}"
                   class="flex items-center gap-1.5 px-3.5 py-2 text-sm font-semibold transition-colors {{ $viewType == 'kanban' ? 'text-white' : 'text-gray-700 hover:bg-gray-50' }}"
                   style="{{ $viewType == 'kanban' ? 'background: '.(($isManager || $isEmployee) ? 'linear-gradient(135deg,#001F5B,#003DA5)' : $page['accent']) : '' }}">
                    <i data-lucide="columns-3" class="w-3.5 h-3.5"></i> Kanban
                </a>
                <a href="{{ route($taskIndexRoute, ['view' => 'list', 'filter' => $filter, 'assignee_id' => request('assignee_id')]) }}"
                   class="flex items-center gap-1.5 px-3.5 py-2 text-sm font-semibold transition-colors {{ $viewType == 'list' ? 'text-white' : 'text-gray-700 hover:bg-gray-50' }}"
                   style="{{ $viewType == 'list' ? 'background: '.(($isManager || $isEmployee) ? 'linear-gradient(135deg,#001F5B,#003DA5)' : $page['accent']) : '' }}">
                    <i data-lucide="list" class="w-3.5 h-3.5"></i> Danh sách
                </a>
            </div>

            <div class="flex gap-2">
                @foreach(['Tất cả', 'Của tôi', 'Quá hạn'] as $f)
                    <a href="{{ route($taskIndexRoute, ['view' => $viewType, 'filter' => $f, 'assignee_id' => request('assignee_id')]) }}"
                       class="px-3.5 py-1.5 rounded-full text-xs font-semibold border transition-all {{ $filter == $f ? 'text-white shadow-sm' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50' }}"
                       style="{{ $filter == $f ? 'background-color: '.(($isManager || $isEmployee) && $f === 'Quá hạn' ? '#E4002B' : $page['accent']).'; border-color: '.(($isManager || $isEmployee) && $f === 'Quá hạn' ? '#E4002B' : $page['accent']) : '' }}">
                        {{ $f }}
                    </a>
                @endforeach
            </div>

            <!-- Assignee Dropdown -->
            <form action="{{ route($taskIndexRoute) }}" method="GET" class="flex items-center gap-2">
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
                    <a href="{{ route($taskIndexRoute, ['view' => $viewType, 'filter' => $filter]) }}" class="text-xs text-red-500 hover:underline font-semibold flex items-center gap-1">
                        <i data-lucide="x-circle" class="w-3.5 h-3.5"></i> Xóa lọc
                    </a>
                @endif
            </form>
        </div>
    </section>

    @if($viewType == 'kanban')
        <section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            @foreach($cols as $key => $col)
                @php
                    if ($isManager) {
                        $colStyle = $managerCols[$key] ?? ['color' => $page['accent'], 'bg' => '#F8FAFC', 'line' => '#CBD5E1'];
                    } elseif ($isEmployee) {
                        $colStyle = $employeeCols[$key] ?? ['color' => $page['accent'], 'bg' => '#F8FAFC', 'line' => '#CBD5E1'];
                    } else {
                        $colStyle = ['color' => $col['color'], 'bg' => $col['bg'], 'line' => '#E5E7EB'];
                    }
                @endphp
                <div class="rounded-[8px] min-h-[300px] flex flex-col border overflow-hidden {{ ($isManager || $isEmployee) ? 'mf-manager-kanban-column' : '' }}" style="background-color: {{ $colStyle['bg'] }}; border-color: {{ $colStyle['line'] }};">
                    <div class="h-1" style="background: {{ ($isManager || $isEmployee) && $key === 'doing' ? 'linear-gradient(90deg,#E4002B,#003DA5)' : $colStyle['color'] }}"></div>
                    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200/70 bg-white/75">
                        <span class="text-sm font-black" style="color: {{ $colStyle['color'] }}">{{ $col['label'] }}</span>
                        <span class="text-xs px-2 py-0.5 rounded-full font-bold text-white" style="background-color: {{ ($isManager || $isEmployee) && $key === 'doing' ? '#E4002B' : $colStyle['color'] }}">{{ count($col['tasks']) }}</span>
                    </div>
                    <div class="p-3 space-y-3 flex-1 overflow-y-auto max-h-[60vh] custom-scrollbar">
                        @forelse($col['tasks'] as $task)
                            @if($isEmployee)
                                <div class="w-full text-left bg-white rounded-[8px] p-4 border border-[#D9E5F7] hover:border-[#003DA5]/40 hover:shadow-[0_14px_28px_rgba(0,61,165,.12)] hover:-translate-y-0.5 transition-all duration-200" id="task-card-{{ $task['id'] }}">
                            @else
                                <button type="button" class="w-full text-left bg-white rounded-[8px] p-4 border {{ $isManager ? 'border-[#D9E5F7] hover:border-[#003DA5]/40 hover:shadow-[0_14px_28px_rgba(0,61,165,.12)]' : 'border-gray-100 hover:shadow-md' }} hover:-translate-y-0.5 transition-all duration-200"
                                        onclick="openTaskDetail({{ json_encode($task) }})">
                            @endif
                                <div class="flex items-center justify-between mb-2">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $task['priority'] == 'Cao' ? 'bg-red-100 text-red-700' : ($task['priority'] == 'Trung bình' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700') }}">{{ $task['priority'] }}</span>
                                    <span class="text-[10px] font-mono text-gray-400">{{ $task['code'] }}</span>
                                </div>
                                <h3 class="text-sm font-bold text-gray-800 mb-1 line-clamp-2">{{ $task['name'] }}</h3>
                                <p class="text-xs text-gray-500 mb-3 truncate">{{ $task['description'] ?: 'Chưa có mô tả chi tiết.' }}</p>
                                <div class="mb-3">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-[10px] text-gray-400">Tiến độ</span>
                                        <span class="text-[10px] font-bold" data-progress-text="{{ $task['id'] }}" style="color: {{ $colStyle['color'] }}">{{ $task['progress'] }}%</span>
                                    </div>
                                    <div class="h-1.5 rounded-full bg-gray-100">
                                        <div class="h-1.5 rounded-full" data-progress-bar="{{ $task['id'] }}" style="width: {{ $task['progress'] }}%; background: {{ ($isManager || $isEmployee) ? 'linear-gradient(90deg,#E4002B,#003DA5)' : $colStyle['color'] }}"></div>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="flex items-center gap-1 text-[10px] {{ $task['status'] == 'Quá hạn' ? 'text-red-600 font-bold' : 'text-gray-400' }}">
                                        <i data-lucide="calendar" class="w-3.5 h-3.5"></i>{{ $task['deadline'] }}
                                    </span>
                                    <span class="flex items-center gap-2">
                                        @if(($task['documents_count'] ?? 0) > 0)
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-gray-500" title="{{ $task['documents_count'] }} tài liệu đính kèm">
                                                <i data-lucide="paperclip" class="w-3.5 h-3.5"></i>{{ $task['documents_count'] }}
                                            </span>
                                        @endif
                                        <span class="w-6 h-6 rounded-full text-white flex items-center justify-center font-bold text-[9px]" style="background-color: {{ $page['accent'] }}" title="{{ $task['assignee'] }}">{{ $task['avatar'] }}</span>
                                    </span>
                                </div>
                                @if($isEmployee)
                                    <div class="mt-3 grid grid-cols-1 sm:grid-cols-[1fr_auto] gap-2">
                                        <select
                                            class="px-3 py-2 rounded-[8px] border border-[#D9E5F7] bg-[#F8FAFF] text-xs font-bold text-[#001F5B] outline-none focus:border-[#003DA5]"
                                            data-task-status="{{ $task['id'] }}"
                                            data-previous-status="{{ $task['status'] }}"
                                            onchange="updateEmployeeTaskStatus({{ $task['id'] }}, this.value)"
                                            {{ ($isEmployee && $task['status'] === 'Hoàn thành') ? 'disabled' : '' }}
                                        >
                                            <option value="Chờ xử lý" {{ $task['status'] === 'Chờ xử lý' ? 'selected' : '' }}>Chờ xử lý</option>
                                            <option value="Đang làm" {{ $task['status'] === 'Đang làm' ? 'selected' : '' }}>Đang làm</option>
                                            <option value="Đang review" {{ $task['status'] === 'Đang review' ? 'selected' : '' }}>Đang review</option>
                                            @if(!$isEmployee || $task['status'] === 'Hoàn thành')
                                                <option value="Hoàn thành" {{ $task['status'] === 'Hoàn thành' ? 'selected' : '' }} {{ $isEmployee ? 'disabled' : '' }}>Hoàn thành</option>
                                            @endif
                                        </select>
                                        <button type="button" onclick="openTaskDetail({{ json_encode($task) }})" class="inline-flex items-center justify-center gap-1.5 rounded-[8px] border border-[#B9CDF5] bg-white px-3 py-2 text-xs font-bold text-[#003DA5] hover:bg-[#F4F8FF]">
                                            <i data-lucide="eye" class="w-3.5 h-3.5"></i>Chi tiết
                                        </button>
                                    </div>
                                </div>
                                @else
                                </button>
                                @endif
                        @empty
                            <div class="text-center py-8 text-xs text-gray-400 border border-dashed rounded-[8px] bg-white/65" style="border-color: {{ $colStyle['line'] }}">Chưa có công việc.</div>
                        @endforelse

                        <button type="button" onclick="openModalForStatus('{{ $col['label'] }}')" class="w-full py-2.5 rounded-[8px] text-xs font-semibold border-2 border-dashed bg-white/70 transition-all hover:bg-white" style="border-color: {{ $colStyle['line'] }}; color: {{ $colStyle['color'] }}">
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
                            <th class="px-5 py-3">Tài liệu</th>
                            <th class="px-5 py-3">Trạng thái</th>
                            <th class="px-5 py-3">Tiến độ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($mappedTasksList as $task)
                            <tr class="hover:bg-gray-50 transition-colors {{ $isEmployee ? '' : 'cursor-pointer' }}" @if(!$isEmployee) onclick="openTaskDetail({{ json_encode($task) }})" @endif>
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
                                <td class="px-5 py-4">
                                    @if(($task['documents_count'] ?? 0) > 0)
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-bold text-gray-700">
                                            <i data-lucide="paperclip" class="w-3.5 h-3.5"></i>{{ $task['documents_count'] }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400">Không có</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    @if($isEmployee)
                                        <select
                                            class="min-w-[132px] rounded-[8px] border border-[#D9E5F7] bg-[#F8FAFF] px-3 py-2 text-xs font-bold text-[#001F5B] outline-none focus:border-[#003DA5]"
                                            data-task-status="{{ $task['id'] }}"
                                            data-previous-status="{{ $task['status'] }}"
                                            onchange="updateEmployeeTaskStatus({{ $task['id'] }}, this.value)"
                                            {{ ($isEmployee && $task['status'] === 'Hoàn thành') ? 'disabled' : '' }}
                                        >
                                            <option value="Chờ xử lý" {{ $task['status'] === 'Chờ xử lý' ? 'selected' : '' }}>Chờ xử lý</option>
                                            <option value="Đang làm" {{ $task['status'] === 'Đang làm' ? 'selected' : '' }}>Đang làm</option>
                                            <option value="Đang review" {{ $task['status'] === 'Đang review' ? 'selected' : '' }}>Đang review</option>
                                            @if(!$isEmployee || $task['status'] === 'Hoàn thành')
                                                <option value="Hoàn thành" {{ $task['status'] === 'Hoàn thành' ? 'selected' : '' }} {{ $isEmployee ? 'disabled' : '' }}>Hoàn thành</option>
                                            @endif
                                        </select>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">{{ $task['status'] }}</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 h-1.5 rounded-full min-w-[80px] bg-gray-100">
                                            <div class="h-1.5 rounded-full" data-progress-bar="{{ $task['id'] }}" style="width: {{ $task['progress'] }}%; background-color: {{ $page['accent'] }}"></div>
                                        </div>
                                        <span class="text-xs w-8 text-right font-mono text-gray-400" data-progress-text="{{ $task['id'] }}">{{ $task['progress'] }}%</span>
                                        @if($isEmployee)
                                            <button type="button" onclick="openTaskDetail({{ json_encode($task) }})" class="ml-2 inline-flex items-center gap-1 rounded-[8px] border border-[#B9CDF5] bg-white px-2.5 py-1.5 text-xs font-bold text-[#003DA5] hover:bg-[#F4F8FF]">
                                                <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center py-8 text-gray-400">Không có công việc nào.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @endif
</div>

<div id="task-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 hidden">
    <div class="bg-white rounded-[8px] w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl">
        <div class="px-6 py-5 border-b border-gray-200 {{ $isDirector ? 'mf-director-task-hero text-white' : '' }}">
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

        <form id="task-form" action="{{ route($taskSaveRoute) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
            @csrf
            <input type="hidden" name="is_proposal" id="form-is-proposal" value="0">
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
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-sm font-semibold text-gray-700 m-0">{{ $isDirector ? 'Người cùng làm *' : 'Nhân viên cùng làm *' }}</label>
                        <label class="inline-flex items-center gap-1.5 text-xs font-bold cursor-pointer user-select-none m-0" style="color: {{ $page['accent'] }}">
                            <input type="checkbox" id="select-all-assignees" class="cursor-pointer rounded border-gray-300 focus:ring-0" style="width: 14px; height: 14px; margin: 0; accent-color: {{ $page['accent'] }}">
                            Chọn tất cả
                        </label>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-56 overflow-auto p-2 border border-gray-200 rounded-[8px] bg-gray-50">
                        @foreach($allUsers as $u)
                            <label class="flex items-center gap-2 bg-white border border-gray-200 rounded-[8px] px-3 py-2 cursor-pointer">
                                <input type="checkbox" name="assigned_to[]" value="{{ $u->id }}" class="accent-[color:var(--accent)]" style="--accent: {{ $page['accent'] }}">
                                <span class="text-sm font-bold text-gray-700">
                                    {{ $u->name }}
                                    <small class="block text-[11px] font-medium text-gray-400">{{ $u->department->TENPHONG ?? $u->department->name ?? 'Chưa có phòng' }}{{ $isDirector ? ' / '.($u->role->name ?? 'Chưa có chức vụ') : '' }}</small>
                                </span>
                            </label>
                        @endforeach
                    </div>
                    <p class="mt-1.5 text-xs text-gray-400">Tích nhiều nhân viên để cùng làm chung một công việc.</p>
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
                        <div class="px-4 py-3 rounded-[8px] bg-[#F4F8FF] text-sm font-semibold text-[#003DA5] border border-[#B9CDF5]">Chờ quản lý xem xét</div>
                    @endif
                </div>
            </div>

            <div class="rounded-[8px] p-4 border {{ $isDirector ? 'bg-[#F8FAFC] border-[#E2E8F0]' : (($isManager || $isEmployee) ? 'bg-[#F4F8FF] border-[#B9CDF5]' : 'bg-[#F0FDF4] border-[#BBF7D0]') }}">
                <div class="flex items-start gap-3">
                    <i data-lucide="{{ $isEmployee ? 'info' : 'shield-check' }}" class="w-5 h-5 mt-0.5" style="color: {{ $page['accent'] }}"></i>
                    <div>
                        <p class="text-sm font-bold text-gray-800">{{ $isEmployee ? 'Bạn không thể giao việc cho người khác' : 'Phạm vi được kiểm soát theo chức vụ' }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $isDirector ? 'Giám đốc có phạm vi toàn công ty.' : ($isManager ? 'Trưởng phòng chỉ giao việc cho nhân viên trong phòng.' : 'Đề xuất sẽ được tạo cho chính bạn ở trạng thái chờ xử lý.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Độ ưu tiên -->
            <div>
                <label class="block text-sm font-semibold mb-1.5 text-gray-700">Độ ưu tiên *</label>
                <select name="priority" required class="w-full px-4 py-3 border border-gray-200 rounded-[8px] text-sm outline-none bg-white focus:border-[color:var(--accent)]" style="--accent: {{ $page['accent'] }}">
                    <option value="Thấp">Thấp</option>
                    <option value="Trung bình" selected>Trung bình</option>
                    <option value="Cao">Cao</option>
                </select>
            </div>

            <!-- File Upload -->
            <div>
                <label class="block text-sm font-semibold mb-1.5 text-gray-700">Tài liệu đính kèm (Tối đa 5 file, &lt; 20MB/file)</label>
                <input type="file" name="attachments[]" multiple class="w-full px-4 py-2 border border-gray-200 rounded-[8px] text-sm outline-none focus:border-[#003DA5] bg-gray-50/50" />
            </div>

            <!-- Current attachments list to delete (for edit mode) -->
            <div id="edit-attachments-container" class="hidden">
                <label class="block text-xs font-bold mb-1.5 text-red-600">Tài liệu hiện tại (Chọn để xóa):</label>
                <div id="edit-attachments-list" class="space-y-1.5 max-h-32 overflow-y-auto p-3 rounded-[8px] border border-dashed border-gray-200 bg-gray-50">
                    <!-- Dynamic rendering -->
                </div>
            </div>

            <!-- Email Notification toggle -->
            <div class="flex items-center justify-between p-4 rounded-[8px] bg-[#E8F0FE]">
                <div>
                    <p class="text-sm font-semibold text-[#001F5B]">Gửi thông báo email khi tạo task</p>
                    <p class="text-xs text-gray-400 mt-0.5">Thông báo tới người thực hiện và quản lý</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="send_email" value="1" class="sr-only peer" checked>
                    <div class="w-10 h-5 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:height-4 after:w-4 after:transition-all peer-checked:bg-[#003DA5]"></div>
                </label>
            </div>

            <!-- Buttons -->
            <div class="pt-4 border-t border-gray-200 flex items-center justify-end gap-3">
                <button type="button" id="cancel-task-modal" class="px-5 py-2.5 rounded-[8px] text-sm font-semibold border border-gray-200 text-gray-700 hover:bg-gray-50">Hủy</button>
                <button type="submit" id="task-submit-btn" class="px-5 py-2.5 rounded-[8px] text-sm font-bold text-white" style="background-color: {{ $page['accent'] }}">{{ $page['submit'] }}</button>
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
            <h2 class="text-2xl font-black text-[#001F5B]" id="detail-name"></h2>
            <div class="flex items-center gap-4">
                <span id="detail-priority-badge" class="px-2.5 py-0.5 rounded-full text-xs font-semibold"></span>
                <span class="text-xs text-gray-400" id="detail-deadline"></span>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-semibold mb-1">Mô tả</p>
                <p class="text-sm text-gray-700 leading-relaxed bg-gray-50 p-4 rounded-[8px]" id="detail-desc"></p>
            </div>
            <div>
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs text-gray-400 font-semibold">Tài liệu đính kèm</p>
                    <span class="text-xs font-bold" id="detail-documents-count" style="color: {{ $page['accent'] }}"></span>
                </div>
                <div id="detail-documents" class="space-y-2 rounded-[8px] border border-gray-100 bg-gray-50 p-3"></div>
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

            <!-- Documents Attachments list -->
            <div id="detail-attachments-box" class="hidden">
                <p class="text-xs text-gray-400 font-semibold mb-1.5">TÀI LIỆU ĐÍNH KÈM</p>
                <div class="space-y-2 max-h-48 overflow-y-auto custom-scrollbar" id="detail-attachments-list">
                    <!-- Dynamic list items -->
                </div>
            </div>

            <!-- Admin/Manager Action Buttons -->
            @if(Auth::user() && (Auth::user()->isDirector() || Auth::user()->isLeader()))
                <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3 mt-2" id="detail-actions">
                    <div id="proposal-action-container" class="flex items-center gap-3"></div>
                    <button type="button" id="edit-task-btn" class="flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-semibold border border-gray-200 text-gray-700 hover:bg-gray-50 transition-all">
                        <i class="fa-solid fa-pen-to-square"></i> Chỉnh sửa
                    </button>
                    <form id="delete-task-form" action="" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa công việc này không?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-white bg-red-600 hover:bg-red-700 transition-all">
                            <i class="fa-solid fa-trash-can"></i> Xóa việc
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Serialize mapped tasks list from server to client
    const mappedTasks = @json($mappedTasksList);

    function getFileIconClass(type) {
        type = (type || '').toLowerCase();
        if (type === 'pdf') return 'fa-solid fa-file-pdf text-red-500';
        if (['doc', 'docx'].includes(type)) return 'fa-solid fa-file-word text-blue-500';
        if (['xls', 'xlsx'].includes(type)) return 'fa-solid fa-file-excel text-green-600';
        if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(type)) return 'fa-solid fa-file-image text-purple-500';
        if (['zip', 'rar'].includes(type)) return 'fa-solid fa-file-zip text-amber-500';
        return 'fa-solid fa-file text-gray-400';
    }

    document.addEventListener("DOMContentLoaded", function() {
        const modal = document.getElementById('task-modal');
        const openBtn = document.getElementById('open-task-modal');
        const closeBtn = document.getElementById('close-task-modal');
        const cancelBtn = document.getElementById('cancel-task-modal');
        
        const form = document.getElementById('task-form');
        const modalTitle = document.getElementById('modal-title');
        const submitBtn = document.getElementById('task-submit-btn');
        const statusSelect = document.getElementById('task-status');

        let currentTask = null;

        const selectAllCheckbox = document.getElementById('select-all-assignees');
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('#task-modal input[name="assigned_to[]"]');
                checkboxes.forEach(cb => {
                    cb.checked = selectAllCheckbox.checked;
                });
            });

            const individualCheckboxes = document.querySelectorAll('#task-modal input[name="assigned_to[]"]');
            
            const updateSelectAllState = () => {
                const checkedCount = document.querySelectorAll('#task-modal input[name="assigned_to[]"]:checked').length;
                selectAllCheckbox.checked = checkedCount === individualCheckboxes.length && individualCheckboxes.length > 0;
            };

            individualCheckboxes.forEach(cb => {
                cb.addEventListener('change', updateSelectAllState);
            });

            if (form) {
                form.addEventListener('reset', function() {
                    setTimeout(updateSelectAllState, 0);
                });
            }
        }

        const openModal = () => modal.classList.remove('hidden');
        const closeModal = () => modal.classList.add('hidden');

        // Reset form to Create mode
        const setCreateMode = () => {
            if (modalTitle) modalTitle.innerText = "{{ $page['formTitle'] ?? 'Tạo công việc mới' }}";
            if (submitBtn) {
                submitBtn.innerText = "{{ $page['submit'] ?? 'Tạo công việc' }}";
            }
            form.action = "{{ route('dashboard.tasks.save') }}";
            form.reset();
            document.getElementById('form-is-proposal').value = '0';
            const assigneeSection = form.querySelector('[name="assigned_to[]"]')?.closest('div');
            if (assigneeSection) {
                assigneeSection.style.display = 'block';
            }
            if (statusSelect) statusSelect.value = 'Chờ xử lý';
            const editAttachContainer = document.getElementById('edit-attachments-container');
            if (editAttachContainer) editAttachContainer.classList.add('hidden');
        };

        if (openBtn) {
            openBtn.addEventListener('click', () => {
                setCreateMode();
                openModal();
            });
        }

        const openProposalBtn = document.getElementById('open-proposal-modal');
        if (openProposalBtn) {
            openProposalBtn.addEventListener('click', () => {
                setCreateMode();
                document.getElementById('form-is-proposal').value = '1';
                if (modalTitle) modalTitle.innerText = "Gửi đề xuất công việc lên cấp trên";
                if (submitBtn) submitBtn.innerText = "Gửi đề xuất";
                const assigneeSection = form.querySelector('[name="assigned_to[]"]')?.closest('div');
                if (assigneeSection) {
                    assigneeSection.style.display = 'none';
                }
                openModal();
            });
        }

        if (closeBtn) closeBtn.addEventListener('click', closeModal);
        if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

        window.openModalForStatus = (label) => {
            setCreateMode();
            if (statusSelect) {
                if (statusSelect.tagName === 'SELECT') {
                    if (label.includes('Đang làm')) statusSelect.value = 'Đang làm';
                    else if (label.includes('Đang review') && Array.from(statusSelect.options).some(option => option.value === 'Đang review')) statusSelect.value = 'Đang review';
                    else if (label.includes('Hoàn thành') && Array.from(statusSelect.options).some(option => option.value === 'Hoàn thành')) statusSelect.value = 'Hoàn thành';
                    else statusSelect.value = 'Chờ xử lý';
                } else {
                    statusSelect.value = 'Chờ xử lý';
                }
            }
            openModal();
        };

        const detailModal = document.getElementById('detail-modal');
        const closeDetailBtn = document.getElementById('close-detail-modal');
        const detailDocuments = document.getElementById('detail-documents');
        const detailDocumentsCount = document.getElementById('detail-documents-count');
        const employeeTaskProgressUrl = '{{ url('/employee/tasks') }}';
        const csrfToken = '{{ csrf_token() }}';
        const employeeProgressByStatus = {
            'Chờ xử lý': 0,
            'Đang làm': 50,
            'Đang review': 80,
            'Hoàn thành': 100,
        };

        window.updateEmployeeTaskStatus = (taskId, status) => {
            const selects = document.querySelectorAll(`[data-task-status="${taskId}"]`);
            const previous = Array.from(selects).find((select) => select.dataset.previousStatus)?.dataset.previousStatus
                || Array.from(selects).find((select) => select.defaultValue)?.defaultValue
                || status;
            const progress = employeeProgressByStatus[status] ?? 0;

            selects.forEach((select) => {
                select.disabled = true;
                select.dataset.previousStatus = previous;
            });

            fetch(`${employeeTaskProgressUrl}/${taskId}/progress`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ status, progress }),
            })
                .then((response) => response.json().then((payload) => ({ ok: response.ok, payload })))
                .then(({ ok, payload }) => {
                    if (!ok || !payload.ok) {
                        throw new Error(payload.message || 'Không thể cập nhật trạng thái.');
                    }

                    const nextProgress = payload.progress ?? progress;
                    document.querySelectorAll(`[data-progress-bar="${taskId}"]`).forEach((bar) => {
                        bar.style.width = `${nextProgress}%`;
                    });
                    document.querySelectorAll(`[data-progress-text="${taskId}"]`).forEach((text) => {
                        text.textContent = `${nextProgress}%`;
                    });
                    selects.forEach((select) => {
                        select.value = payload.status || status;
                        select.dataset.previousStatus = payload.status || status;
                    });
                })
                .catch(() => {
                    selects.forEach((select) => {
                        select.value = select.dataset.previousStatus || previous;
                    });
                    alert('Không cập nhật được trạng thái công việc. Vui lòng thử lại.');
                })
                .finally(() => {
                    selects.forEach((select) => {
                        select.disabled = false;
                    });
                });
        };

        const renderDetailDocuments = (documents = []) => {
            detailDocuments.innerHTML = '';
            detailDocumentsCount.innerText = documents.length ? documents.length + ' file' : '';

            if (!documents.length) {
                const empty = document.createElement('div');
                empty.className = 'flex items-center gap-2 text-xs text-gray-400';
                empty.innerHTML = '<i data-lucide="folder-open" class="w-4 h-4"></i><span>Chưa có tài liệu đính kèm.</span>';
                detailDocuments.appendChild(empty);
                lucide.createIcons();
                return;
            }

            documents.forEach((file) => {
                const row = document.createElement('div');
                row.className = 'flex flex-col gap-3 rounded-[8px] border border-gray-200 bg-white p-3 sm:flex-row sm:items-center sm:justify-between';

                const info = document.createElement('div');
                info.className = 'min-w-0';

                const nameLine = document.createElement('div');
                nameLine.className = 'flex min-w-0 items-center gap-2 text-sm font-bold text-gray-800';
                nameLine.innerHTML = '<i data-lucide="paperclip" class="h-4 w-4 shrink-0 text-gray-400"></i>';

                const fileName = document.createElement('span');
                fileName.className = 'truncate';
                fileName.textContent = file.file_name || 'Tài liệu';
                nameLine.appendChild(fileName);

                const meta = document.createElement('div');
                meta.className = 'mt-1 text-xs text-gray-400';
                meta.textContent = [file.file_type, file.uploader, file.uploaded_at].filter(Boolean).join(' · ');

                info.appendChild(nameLine);
                info.appendChild(meta);

                const actions = document.createElement('div');
                actions.className = 'flex shrink-0 gap-2';

                const preview = document.createElement('a');
                preview.className = 'inline-flex items-center gap-1.5 rounded-[8px] border border-gray-200 px-3 py-1.5 text-xs font-bold text-gray-700 hover:bg-gray-50';
                preview.href = file.preview_url;
                preview.target = '_blank';
                preview.rel = 'noopener';
                preview.innerHTML = '<i data-lucide="eye" class="w-3.5 h-3.5"></i>Xem';

                const download = document.createElement('a');
                download.className = 'inline-flex items-center gap-1.5 rounded-[8px] px-3 py-1.5 text-xs font-bold text-white';
                download.style.backgroundColor = '{{ $page['accent'] }}';
                download.href = file.download_url;
                download.innerHTML = '<i data-lucide="download" class="w-3.5 h-3.5"></i>Tải';

                actions.appendChild(preview);
                actions.appendChild(download);

                if (file.can_forward && file.forward_url) {
                    const forwardBtn = document.createElement('button');
                    forwardBtn.type = 'button';
                    forwardBtn.className = 'inline-flex items-center gap-1.5 rounded-[8px] border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-700 hover:bg-blue-100 transition-colors';
                    forwardBtn.innerHTML = '<i data-lucide="send" class="w-3.5 h-3.5"></i>Chuyển Giám đốc';
                    forwardBtn.onclick = () => forwardDocumentToDirector(file.id, file.forward_url, forwardBtn);
                    actions.appendChild(forwardBtn);
                }

                if (file.can_delete !== false) {
                    const deleteBtn = document.createElement('button');
                    deleteBtn.type = 'button';
                    deleteBtn.className = 'inline-flex items-center gap-1.5 rounded-[8px] border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-bold text-red-600 hover:bg-red-100 transition-colors';
                    deleteBtn.innerHTML = '<i data-lucide="trash-2" class="w-3.5 h-3.5"></i>Xóa';
                    deleteBtn.onclick = () => deleteDocumentItem(file.id, row);
                    actions.appendChild(deleteBtn);
                }

                row.appendChild(info);
                row.appendChild(actions);
                detailDocuments.appendChild(row);
            });

            lucide.createIcons();
        };

        window.forwardDocumentToDirector = (documentId, forwardUrl, btnElement) => {
            if (!confirm('Bạn có chắc chắn muốn chuyển file này lên Giám đốc?')) return;

            fetch(forwardUrl, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(res => {
                if (res.ok) {
                    btnElement.remove();
                    alert('Đã chuyển file đính kèm lên Giám đốc.');
                } else {
                    alert('Không thể chuyển file.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Có lỗi xảy ra khi chuyển file.');
            });
        };

        window.deleteDocumentItem = (documentId, rowElement) => {
            if (!confirm('Bạn có chắc chắn muốn xóa tệp đính kèm này không?')) return;

            fetch(`{{ url('/tai-lieu') }}/${documentId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (rowElement) {
                        rowElement.remove();
                        if (detailDocuments.children.length === 0) {
                            renderDetailDocuments([]);
                        }
                    }
                    alert(data.message || 'Đã xóa tệp đính kèm.');
                } else {
                    alert(data.message || 'Không thể xóa tệp.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Có lỗi xảy ra khi xóa tệp đính kèm.');
            });
        };

        window.escalateProposal = (taskId) => {
            if (!confirm('Bạn có chắc chắn muốn duyệt và chuyển đề xuất này lên Ban Giám đốc?')) return;
            
            const f = document.createElement('form');
            f.method = 'POST';
            f.action = `/dashboard/tasks/${taskId}/escalate`;
            f.innerHTML = `<input type="hidden" name="_token" value="${csrfToken}">`;
            document.body.appendChild(f);
            f.submit();
        };

        window.approveProposal = (taskId) => {
            if (!confirm('Bạn có chắc chắn muốn phê duyệt đề xuất này thành công việc chính thức?')) return;
            
            const f = document.createElement('form');
            f.method = 'POST';
            f.action = `/dashboard/tasks/${taskId}/approve-proposal`;
            f.innerHTML = `<input type="hidden" name="_token" value="${csrfToken}">`;
            document.body.appendChild(f);
            f.submit();
        };

        window.openApproveProposalModal = (task) => {
            detailModal.classList.add('hidden');
            
            if (modalTitle) modalTitle.innerText = "Giao việc (Phê duyệt đề xuất)";
            if (submitBtn) submitBtn.innerText = "Giao việc";
            form.action = `/dashboard/tasks/${task.id}/update`;
            
            // Populate inputs
            form.querySelector('[name="task_name"]').value = task.name;
            form.querySelector('[name="description"]').value = task.description || '';
            form.querySelector('[name="deadline"]').value = task.deadline_raw;
            if (statusSelect) statusSelect.value = 'Todo';
            form.querySelector('[name="priority"]').value = task.priority;
            
            // Set is_proposal input to 0 (so it converts to normal task upon save)
            document.getElementById('form-is-proposal').value = '0';
            
            // Show assignee selection
            const assigneeSection = form.querySelector('[name="assigned_to[]"]')?.closest('div');
            if (assigneeSection) {
                assigneeSection.style.display = 'block';
            }
            
            // Check proposer checkbox
            const proposerId = task.assignee_id || task.assigned_by;
            const checkboxes = form.querySelectorAll('input[name="assigned_to[]"]');
            checkboxes.forEach(cb => {
                cb.checked = (parseInt(cb.value) === parseInt(proposerId));
            });
            
            openModal();
        };

        window.openTaskDetail = (task) => {
            currentTask = task;
            
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

            renderDetailDocuments(task.documents || []);

            // Handle proposal action buttons
            const proposalActionContainer = document.getElementById('proposal-action-container');
            if (proposalActionContainer) {
                proposalActionContainer.innerHTML = '';
                
                if (task.is_proposal) {
                    const isDirector = {{ $isDirector ? 'true' : 'false' }};
                    const isLeader = {{ $isManager ? 'true' : 'false' }};
                    
                    if (isLeader && task.proposal_step === 1) {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 transition-all';
                        btn.innerHTML = '<i class="fa-solid fa-share-from-square"></i> Duyệt & Gửi lên Ban Giám đốc';
                        btn.onclick = () => escalateProposal(task.id);
                        proposalActionContainer.appendChild(btn);
                    } else if (isDirector && task.proposal_step === 2) {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-white bg-green-600 hover:bg-green-700 transition-all';
                        btn.innerHTML = '<i class="fa-solid fa-circle-check"></i> Giao việc (Phê duyệt)';
                        btn.onclick = () => openApproveProposalModal(task);
                        proposalActionContainer.appendChild(btn);
                    }
                }
            }

            // Bind Delete Form Action
            const deleteForm = document.getElementById('delete-task-form');
            if (deleteForm) {
                deleteForm.action = `/dashboard/tasks/${task.id}/delete`;
            }

            detailModal.classList.remove('hidden');
        };

        closeDetailBtn?.addEventListener('click', () => {
            detailModal.classList.add('hidden');
        });

        // Edit button click flow
        const editBtn = document.getElementById('edit-task-btn');
        if (editBtn) {
            editBtn.addEventListener('click', () => {
                if (!currentTask) return;
                
                // Hide details, open edit modal
                detailModal.classList.add('hidden');
                
                if (modalTitle) modalTitle.innerText = "Chỉnh sửa công việc";
                if (submitBtn) submitBtn.innerText = "Cập nhật";
                form.action = `/dashboard/tasks/${currentTask.id}/update`;
                
                // Populate inputs
                form.querySelector('[name="task_name"]').value = currentTask.name;
                form.querySelector('[name="description"]').value = currentTask.description || '';
                if (form.querySelector('[name="priority"]')) {
                    form.querySelector('[name="priority"]').value = currentTask.priority;
                }
                if (form.querySelector('[name="status"]')) {
                    form.querySelector('[name="status"]').value = currentTask.status;
                }
                if (form.querySelector('[name="assigned_to[]"]')) {
                    form.querySelector('[name="assigned_to[]"]').value = currentTask.assignee_id || '';
                } else if (form.querySelector('[name="assigned_to"]')) {
                    form.querySelector('[name="assigned_to"]').value = currentTask.assignee_id || '';
                }
                form.querySelector('[name="deadline"]').value = currentTask.deadline_raw || '';
                
                // Populate attachments list for deletion
                const editAttachContainer = document.getElementById('edit-attachments-container');
                const editAttachList = document.getElementById('edit-attachments-list');
                editAttachList.innerHTML = '';
                
                if (currentTask.documents && currentTask.documents.length > 0) {
                    editAttachContainer.classList.remove('hidden');
                    currentTask.documents.forEach(att => {
                        const lbl = document.createElement('label');
                        lbl.className = "flex items-center gap-2 p-1.5 hover:bg-white rounded-lg cursor-pointer text-xs font-semibold text-gray-700";
                        lbl.innerHTML = `
                            <input type="checkbox" name="delete_attachments[]" value="${att.id}" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                            <span class="text-red-500"><i class="fa-solid fa-trash-can text-[10px]"></i></span>
                            <span class="truncate max-w-[200px]">${att.file_name}</span>
                        `;
                        editAttachList.appendChild(lbl);
                    });
                } else {
                    editAttachContainer.classList.add('hidden');
                }
                
                modal.classList.remove('hidden');
            });
        }

        // Auto open task detail if task_id is present in URL
        const urlParams = new URLSearchParams(window.location.search);
        const taskIdParam = urlParams.get('task_id');
        if (taskIdParam) {
            const foundTask = mappedTasks.find(t => String(t.id) === taskIdParam);
            if (foundTask) {
                openTaskDetail(foundTask);
            }
        }
    });
</script>
@endsection
