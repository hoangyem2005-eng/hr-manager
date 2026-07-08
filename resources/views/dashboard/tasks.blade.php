@extends('layouts.dashboard')

@section('title', 'Quản lý Công việc - MobiFone WorkHub')
@section('page_title', 'Công việc')

@section('content')
<div class="space-y-5">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-[#001F5B]">Quản lý Công việc</h1>
        @if(Auth::user() && (Auth::user()->isDirector() || Auth::user()->isLeader()))
            <button id="open-task-modal"
                    class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white bg-[#003DA5] hover:bg-[#0057C8] hover:shadow-lg transition-all active:scale-95">
                <i data-lucide="plus" class="w-4 h-4"></i> Tạo công việc mới
            </button>
        @endif
    </div>

    <!-- Toolbar: View Switcher and Filters -->
    <div class="flex items-center gap-3 flex-wrap justify-between">
        <div class="flex items-center gap-3 flex-wrap">
            <!-- View Switcher -->
            <div class="flex rounded-xl border border-gray-200 bg-white overflow-hidden">
                <a href="{{ route('dashboard.tasks', ['view' => 'kanban', 'filter' => $filter, 'assignee_id' => request('assignee_id')]) }}"
                   class="flex items-center gap-1.5 px-3.5 py-2 text-sm font-semibold transition-colors {{ $viewType == 'kanban' ? 'bg-[#003DA5] text-white' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i data-lucide="columns" class="w-3.5 h-3.5"></i> Kanban
                </a>
                <a href="{{ route('dashboard.tasks', ['view' => 'list', 'filter' => $filter, 'assignee_id' => request('assignee_id')]) }}"
                   class="flex items-center gap-1.5 px-3.5 py-2 text-sm font-semibold transition-colors {{ $viewType == 'list' ? 'bg-[#003DA5] text-white' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i data-lucide="list" class="w-3.5 h-3.5"></i> Danh sách
                </a>
            </div>

            <!-- Filters -->
            <div class="flex gap-2">
                @foreach(['Tất cả', 'Của tôi', 'Quá hạn'] as $f)
                    <a href="{{ route('dashboard.tasks', ['view' => $viewType, 'filter' => $f, 'assignee_id' => request('assignee_id')]) }}"
                       class="px-3.5 py-1.5 rounded-full text-xs font-semibold border transition-all {{ $filter == $f ? 'bg-[#003DA5] text-white border-[#003DA5]' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50' }}">
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

        @if(session('success'))
            <div class="px-4 py-2 rounded-xl text-sm bg-green-50 border border-green-200 text-green-700 flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
    </div>

    <!-- Kanban View -->
    @if($viewType == 'kanban')
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @foreach($cols as $key => $col)
                <div class="rounded-2xl min-h-[300px] flex flex-col" style="background-color: {{ $col['bg'] }};">
                    <!-- Column Header -->
                    <div class="flex items-center justify-between px-4 py-3.5 border-b border-gray-200/50">
                        <span class="text-sm font-bold" style="color: {{ $col['color'] }}">{{ $col['label'] }}</span>
                        <span class="text-xs px-2 py-0.5 rounded-full font-bold text-white" style="background-color: {{ $col['color'] }}">{{ count($col['tasks']) }}</span>
                    </div>

                    <!-- Task List -->
                    <div class="p-3 space-y-3 flex-1 overflow-y-auto max-h-[60vh] custom-scrollbar">
                        @forelse($col['tasks'] as $task)
                            <div class="bg-white rounded-2xl p-4 border border-gray-100 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 cursor-pointer"
                                 onclick="openTaskDetail({{ json_encode($task) }})">
                                <div class="flex items-center justify-between mb-2">
                                    <!-- Priority -->
                                    @if($task['priority'] == 'Cao')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-100 text-red-700">Cao</span>
                                    @elseif($task['priority'] == 'Trung bình')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-700">Trung bình</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-100 text-blue-700">Thấp</span>
                                    @endif

                                    <button class="text-gray-400 hover:text-gray-600"><i data-lucide="more-vertical" class="w-4 h-4"></i></button>
                                </div>

                                <h4 class="text-sm font-bold text-gray-700 mb-1 line-clamp-2">{{ $task['name'] }}</h4>
                                <p class="text-xs text-gray-400 mb-3 truncate">{{ $task['description'] ?: 'Phòng Nhân sự · MobiFone' }}</p>

                                <!-- Progress Bar -->
                                <div class="mb-3">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-[10px] text-gray-400">Tiến độ</span>
                                        <span class="text-[10px] font-bold" style="color: {{ $col['color'] }}">{{ $task['progress'] }}%</span>
                                    </div>
                                    <div class="h-1.5 rounded-full bg-gray-100">
                                        <div class="h-1.5 rounded-full transition-all" style="width: {{ $task['progress'] }}%; background-color: {{ $col['color'] }}"></div>
                                    </div>
                                </div>

                                <!-- Deadline & User Avatar -->
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-1 text-[10px] {{ $task['status'] == 'Quá hạn' ? 'text-red-600 font-bold' : 'text-gray-400' }}">
                                        <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                                        <span>{{ $task['deadline'] }}</span>
                                    </div>
                                    <div class="w-6 h-6 rounded-full bg-[#003DA5] text-white flex items-center justify-center font-bold text-[9px] cursor-pointer" title="{{ $task['assignee'] }}">
                                        {{ $task['avatar'] }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-xs text-gray-400 border border-dashed border-gray-300/40 rounded-xl">Chưa có tác vụ.</div>
                        @endforelse

                        @if(Auth::user() && (Auth::user()->isDirector() || Auth::user()->isLeader()))
                            <button onclick="openModalForStatus('{{ $col['label'] }}')" class="w-full py-2.5 rounded-xl text-xs font-semibold border-2 border-dashed border-gray-200 text-gray-400 hover:border-[#003DA5] hover:text-[#003DA5] transition-all bg-white/40">
                                + Thêm công việc
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- List View -->
    @if($viewType == 'list')
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-[#F1F3F5] text-gray-400 font-semibold text-xs uppercase tracking-wide">
                        <th class="px-5 py-3">Mã CV</th>
                        <th class="px-5 py-3">Tên công việc</th>
                        <th class="px-5 py-3">Người thực hiện</th>
                        <th class="px-5 py-3">Ưu tiên</th>
                        <th class="px-5 py-3">Deadline</th>
                        <th class="px-5 py-3">Trạng thái</th>
                        <th class="px-5 py-3">Tiến độ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($mappedTasksList as $task)
                        <tr class="hover:bg-[#E8F0FE]/30 transition-colors cursor-pointer" onclick="openTaskDetail({{ json_encode($task) }})">
                            <td class="px-5 py-4 text-xs font-mono text-gray-400">{{ $task['code'] }}</td>
                            <td class="px-5 py-4 font-bold text-gray-700 truncate max-w-[200px]">{{ $task['name'] }}</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-[#0057C8] text-white flex items-center justify-center font-bold text-[10px]">
                                        {{ $task['avatar'] }}
                                    </div>
                                    <span class="text-xs text-gray-700">{{ $task['assignee'] }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                @if($task['priority'] == 'Cao')
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">Cao</span>
                                @elseif($task['priority'] == 'Trung bình')
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">Trung bình</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">Thấp</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-xs font-medium {{ $task['status'] == 'Quá hạn' ? 'text-red-600 font-bold' : 'text-gray-400' }}">{{ $task['deadline'] }}</td>
                            <td class="px-5 py-4">
                                @if($task['status'] == 'Hoàn thành')
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Hoàn thành</span>
                                @elseif($task['status'] == 'Đang làm')
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">Đang làm</span>
                                @elseif($task['status'] == 'Đang review')
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">Đang review</span>
                                @elseif($task['status'] == 'Quá hạn')
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Quá hạn</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Chờ xử lý</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 h-1.5 rounded-full min-w-[80px] bg-gray-100">
                                        <div class="h-1.5 rounded-full bg-[#003DA5]" style="width: {{ $task['progress'] }}%"></div>
                                    </div>
                                    <span class="text-xs w-8 text-right font-mono text-gray-400">{{ $task['progress'] }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-gray-400">Không có công việc nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>

<!-- ==================== POPUP: CREATE TASK MODAL ==================== -->
<div id="task-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 hidden">
    <div class="bg-white rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl flex flex-col justify-between">
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-200">
            <h2 class="text-xl font-bold text-[#001F5B]" id="modal-title">Tạo công việc mới</h2>
            <button id="close-task-modal" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-gray-100 transition-colors">
                <i data-lucide="x" class="w-5 h-5 text-gray-400"></i>
            </button>
        </div>

        <form id="task-form" action="{{ route('dashboard.tasks.save') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
            @csrf
            <!-- Task Name -->
            <div>
                <label class="block text-sm font-semibold mb-1.5 text-gray-700">Tên công việc *</label>
                <input name="task_name" required class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm outline-none focus:border-[#003DA5]"
                       placeholder="Nhập tên công việc..." />
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-semibold mb-1.5 text-gray-700">Mô tả</label>
                <textarea name="description" rows="3" class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm outline-none resize-none focus:border-[#003DA5]"
                          placeholder="Mô tả chi tiết công việc..."></textarea>
            </div>

            <!-- Priority & Status -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1.5 text-gray-700">Ưu tiên</label>
                    <select name="priority" class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm outline-none bg-white focus:border-[#003DA5]">
                        <option value="Thấp">🔵 Thấp</option>
                        <option value="Trung bình" selected>🟡 Trung bình</option>
                        <option value="Cao">🔴 Cao</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5 text-gray-700">Trạng thái</label>
                    <select name="status" id="task-status" class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm outline-none bg-white focus:border-[#003DA5]">
                        <option value="Chờ xử lý">Chờ xử lý</option>
                        <option value="Đang làm" selected>Đang làm</option>
                        <option value="Đang review">Đang review</option>
                        <option value="Hoàn thành">Hoàn thành</option>
                    </select>
                </div>
            </div>

            <!-- Assignee -->
            <div>
                <label class="block text-sm font-semibold mb-1.5 text-gray-700">Người thực hiện *</label>
                <select name="assigned_to" required class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm outline-none bg-white focus:border-[#003DA5]">
                    @foreach($allUsers as $u)
                        <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Dates -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1.5 text-gray-700">Ngày bắt đầu</label>
                    <input type="date" name="start_date" class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm outline-none focus:border-[#003DA5]" />
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5 text-gray-700">Deadline *</label>
                    <input type="date" name="deadline" required class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm outline-none focus:border-[#003DA5]" />
                </div>
            </div>

            <!-- File Upload -->
            <div>
                <label class="block text-sm font-semibold mb-1.5 text-gray-700">Tài liệu đính kèm (Tối đa 5 file, < 20MB/file)</label>
                <input type="file" name="attachments[]" multiple class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm outline-none focus:border-[#003DA5] bg-gray-50/50" />
            </div>

            <!-- Current attachments list to delete (for edit mode) -->
            <div id="edit-attachments-container" class="hidden">
                <label class="block text-xs font-bold mb-1.5 text-red-600">Tài liệu hiện tại (Chọn để xóa):</label>
                <div id="edit-attachments-list" class="space-y-1.5 max-h-32 overflow-y-auto p-3 rounded-xl border border-dashed border-gray-200 bg-gray-50">
                    <!-- Dynamic rendering -->
                </div>
            </div>

            <!-- Email Notification toggle -->
            <div class="flex items-center justify-between p-4 rounded-xl bg-[#E8F0FE]">
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
                <button type="button" id="cancel-task-modal" class="px-5 py-2.5 rounded-xl text-sm font-semibold border border-gray-200 text-gray-700 hover:bg-gray-50">Hủy</button>
                <button type="submit" id="task-submit-btn" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-[#003DA5] hover:bg-[#0057C8]">Tạo công việc</button>
            </div>
        </form>
    </div>
</div>

<!-- ==================== POPUP: TASK DETAIL MODAL ==================== -->
<div id="detail-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 hidden">
    <div class="bg-white rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl flex flex-col justify-between p-6">
        <div class="flex items-center justify-between border-b pb-4 mb-4">
            <div class="flex items-center gap-1.5 text-xs text-gray-400">
                <span>Công việc</span><i data-lucide="chevron-right" class="w-3 h-3"></i><span class="text-[#003DA5] font-bold" id="detail-code"></span>
            </div>
            <button id="close-detail-modal" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-gray-100 transition-colors">
                <i data-lucide="x" class="w-5 h-5 text-gray-400"></i>
            </button>
        </div>

        <div class="space-y-5">
            <h2 class="text-2xl font-bold text-[#001F5B]" id="detail-name"></h2>
            
            <div class="flex items-center gap-4">
                <span id="detail-priority-badge" class="px-2.5 py-0.5 rounded-full text-xs font-semibold"></span>
                <span class="text-xs text-gray-400" id="detail-deadline"></span>
            </div>

            <div>
                <p class="text-xs text-gray-400 font-semibold mb-1">MÔ TẢ CHI TIẾT</p>
                <p class="text-sm text-gray-700 leading-relaxed bg-gray-50 p-4 rounded-xl" id="detail-desc"></p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-400 font-semibold mb-1">NGƯỜI THỰC HIỆN</p>
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full bg-[#0057C8] text-white flex items-center justify-center font-bold text-[10px]" id="detail-avatar"></div>
                        <span class="text-sm text-gray-700" id="detail-assignee"></span>
                    </div>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-semibold mb-1">TIẾN ĐỘ</p>
                    <div class="text-sm font-bold text-[#003DA5]" id="detail-progress"></div>
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

        // Toggle modal
        const toggleModal = () => modal.classList.toggle('hidden');

        // Reset form to Create mode
        const setCreateMode = () => {
            modalTitle.innerText = "Tạo công việc mới";
            submitBtn.innerText = "Tạo công việc";
            form.action = "{{ route('dashboard.tasks.save') }}";
            form.reset();
            statusSelect.value = 'Chờ xử lý';
            document.getElementById('edit-attachments-container').classList.add('hidden');
        };

        if (openBtn) {
            openBtn.addEventListener('click', () => {
                setCreateMode();
                toggleModal();
            });
        }
        closeBtn.addEventListener('click', toggleModal);
        cancelBtn.addEventListener('click', toggleModal);

        window.openModalForStatus = (label) => {
            setCreateMode();
            if (label.includes('Chờ xử lý')) statusSelect.value = 'Chờ xử lý';
            else if (label.includes('Đang làm')) statusSelect.value = 'Đang làm';
            else if (label.includes('Đang review')) statusSelect.value = 'Đang review';
            else if (label.includes('Hoàn thành')) statusSelect.value = 'Hoàn thành';
            toggleModal();
        };

        // Task Detail modal handling
        const detailModal = document.getElementById('detail-modal');
        const closeDetailBtn = document.getElementById('close-detail-modal');

        window.openTaskDetail = (task) => {
            currentTask = task;
            
            document.getElementById('detail-code').innerText = task.code || ('WH-' + String(task.id).padStart(3, '0'));
            document.getElementById('detail-name').innerText = task.name;
            document.getElementById('detail-desc').innerText = task.description || 'Không có mô tả chi tiết.';
            document.getElementById('detail-deadline').innerText = 'Hạn chót: ' + task.deadline;
            document.getElementById('detail-assignee').innerText = task.assignee;
            document.getElementById('detail-avatar').innerText = task.avatar;
            document.getElementById('detail-progress').innerText = task.progress + '%';

            // Style priority badge
            const badge = document.getElementById('detail-priority-badge');
            badge.innerText = task.priority;
            badge.className = "px-2.5 py-0.5 rounded-full text-xs font-semibold";
            if (task.priority === 'Cao') {
                badge.classList.add('bg-red-100', 'text-red-700');
            } else if (task.priority === 'Trung bình') {
                badge.classList.add('bg-amber-100', 'text-amber-700');
            } else {
                badge.classList.add('bg-blue-100', 'text-blue-700');
            }

            // Render attachments in detail modal
            const attachBox = document.getElementById('detail-attachments-box');
            const attachList = document.getElementById('detail-attachments-list');
            attachList.innerHTML = '';
            
            if (task.attachments && task.attachments.length > 0) {
                attachBox.classList.remove('hidden');
                task.attachments.forEach(att => {
                    const inlinePreview = ['pdf', 'jpg', 'jpeg', 'png', 'gif'].includes(att.file_type) 
                        ? `<a href="${att.preview_url}" class="text-[10px] text-emerald-600 hover:underline font-bold" target="_blank">Xem</a>` 
                        : '';
                    
                    const div = document.createElement('div');
                    div.className = "flex items-center justify-between p-2 rounded-xl border border-gray-100 bg-gray-50/50 hover:bg-gray-50 transition-colors";
                    div.innerHTML = `
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="text-gray-400"><i class="${getFileIconClass(att.file_type)}"></i></span>
                            <span class="text-xs text-gray-700 font-semibold truncate max-w-[240px]" title="${att.file_name}">${att.file_name}</span>
                            <span class="text-[9px] text-gray-400 font-mono">(${att.uploader})</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="${att.download_url}" class="text-[10px] text-[#003DA5] hover:underline font-bold" target="_blank">Tải về</a>
                            ${inlinePreview}
                        </div>
                    `;
                    attachList.appendChild(div);
                });
            } else {
                attachBox.classList.add('hidden');
            }

            // Bind Delete Form Action
            const deleteForm = document.getElementById('delete-task-form');
            if (deleteForm) {
                deleteForm.action = `/dashboard/tasks/${task.id}/delete`;
            }

            detailModal.classList.remove('hidden');
        };

        closeDetailBtn.addEventListener('click', () => {
            detailModal.classList.add('hidden');
        });

        // Edit button click flow
        const editBtn = document.getElementById('edit-task-btn');
        if (editBtn) {
            editBtn.addEventListener('click', () => {
                if (!currentTask) return;
                
                // Hide details, open edit modal
                detailModal.classList.add('hidden');
                
                modalTitle.innerText = "Chỉnh sửa công việc";
                submitBtn.innerText = "Cập nhật";
                form.action = `/dashboard/tasks/${currentTask.id}/update`;
                
                // Populate inputs
                form.querySelector('[name="task_name"]').value = currentTask.name;
                form.querySelector('[name="description"]').value = currentTask.description || '';
                form.querySelector('[name="priority"]').value = currentTask.priority;
                form.querySelector('[name="status"]').value = currentTask.status;
                form.querySelector('[name="assigned_to"]').value = currentTask.assignee_id || '';
                form.querySelector('[name="deadline"]').value = currentTask.deadline_raw || '';
                
                // Populate attachments list for deletion
                const editAttachContainer = document.getElementById('edit-attachments-container');
                const editAttachList = document.getElementById('edit-attachments-list');
                editAttachList.innerHTML = '';
                
                if (currentTask.attachments && currentTask.attachments.length > 0) {
                    editAttachContainer.classList.remove('hidden');
                    currentTask.attachments.forEach(att => {
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
