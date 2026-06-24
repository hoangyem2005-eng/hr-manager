@extends('layouts.dashboard')

@section('title', 'Thông báo & Cấu hình - MobiFone WorkHub')
@section('page_title', 'Thông báo')

@section('content')
<div class="space-y-5">
    <h1 class="text-2xl font-bold text-[#001F5B]">Thông báo & Email</h1>
    
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">
        <!-- Left: Notification List -->
        <div class="lg:col-span-3 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col justify-between">
            <div>
                <!-- Tabs -->
                <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between flex-wrap gap-2">
                    <div class="flex gap-1.5" id="notif-tabs">
                        <button onclick="switchTab('all', this)" class="px-3.5 py-1.5 rounded-full text-xs font-semibold bg-[#003DA5] text-white">Tất cả</button>
                        <button onclick="switchTab('unread', this)" class="px-3.5 py-1.5 rounded-full text-xs font-semibold text-gray-400 hover:text-gray-600 flex items-center gap-1.5">
                            Chưa đọc <span class="px-1.5 py-0.5 rounded-full bg-[#E4002B] text-white font-bold text-[9px]">2</span>
                        </button>
                        <button onclick="switchTab('task', this)" class="px-3.5 py-1.5 rounded-full text-xs font-semibold text-gray-400 hover:text-gray-600">Task giao</button>
                        <button onclick="switchTab('deadline', this)" class="px-3.5 py-1.5 rounded-full text-xs font-semibold text-gray-400 hover:text-gray-600">Nhắc deadline</button>
                    </div>
                    <button onclick="markAllAsRead()" class="text-xs font-semibold text-[#003DA5] hover:underline">Đánh dấu tất cả đã đọc</button>
                </div>

                <!-- List items -->
                <div class="divide-y divide-gray-50" id="notif-list-container">
                    @foreach($notifications as $n)
                        @php
                            $colors = [
                                'task' => ['bg' => '#EFF6FF', 'text' => '#003DA5', 'icon' => 'check-square'],
                                'overdue' => ['bg' => '#FEF2F2', 'text' => '#DC2626', 'icon' => 'alert-circle'],
                                'deadline' => ['bg' => '#FFFBEB', 'text' => '#D97706', 'icon' => 'clock'],
                                'complete' => ['bg' => '#F0FDF4', 'text' => '#16A34A', 'icon' => 'check-circle'],
                            ];
                            $c = $colors[$n['type']] ?? $colors['task'];
                        @endphp
                        <div class="flex gap-4 p-4 hover:bg-gray-50/70 transition-colors cursor-pointer notif-item {{ !$n['read'] ? 'bg-[#E8F0FE]/40' : '' }}" 
                             data-type="{{ $n['type'] }}" 
                             data-read="{{ $n['read'] ? 'true' : 'false' }}"
                             onclick="markAsRead(this)">
                            <!-- Icon Circle -->
                            <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0" style="background-color: {{ $c['bg'] }}; color: {{ $c['text'] }};">
                                <i data-lucide="{{ $c['icon'] }}" class="w-5.5 h-5.5"></i>
                            </div>

                            <!-- Text Content -->
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-[#001F5B]">{{ $n['title'] }}</p>
                                <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $n['desc'] }}</p>
                                <p class="text-[10px] text-gray-400 mt-1.5 font-mono">{{ $n['time'] }}</p>
                            </div>

                            <!-- Dot indicator -->
                            <div class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0 {{ !$n['read'] ? 'bg-[#003DA5]' : 'bg-transparent' }} unread-dot"></div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right: Email Preview & Settings -->
        <div class="lg:col-span-2 space-y-4">
            <!-- Email Preview Widget -->
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="font-bold mb-3 text-[#001F5B]">Xem trước email thông báo</h3>
                    <select id="email-preview-type" onchange="changeEmailPreview(this.value)" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl outline-none bg-white focus:border-[#003DA5]">
                        <option value="task">Task được giao</option>
                        <option value="deadline">Deadline sắp đến</option>
                        <option value="overdue">Task quá hạn</option>
                    </select>
                </div>
                <div class="p-4 bg-gray-50">
                    <div class="border border-gray-200 rounded-xl overflow-hidden text-[11px] bg-white shadow-sm">
                        <!-- Headers -->
                        <div class="px-4 py-3 border-b border-gray-100 space-y-1 bg-gray-50 text-gray-400">
                            <p>Đến: <span class="text-gray-700 font-medium">an.nguyen@mobifone.vn</span></p>
                            <p id="email-subject">Tiêu đề: [WorkHub] Bạn được giao công việc mới</p>
                        </div>
                        <!-- Logo banner -->
                        <div class="py-3 px-4 text-white text-center text-sm font-bold bg-[#003DA5]">
                            🔷 MobiFone WorkHub
                        </div>
                        <!-- Content -->
                        <div class="p-4 space-y-4 text-gray-700 leading-relaxed">
                            <p class="text-sm">Xin chào <strong>Nguyễn Văn An</strong>,</p>
                            <p id="email-body-text">Bạn được giao công việc mới trong hệ thống WorkHub:</p>
                            
                            <!-- Card box -->
                            <div class="p-3.5 rounded-xl border border-gray-100 bg-gray-50" id="email-card-box">
                                <p class="font-bold text-[#001F5B] text-xs" id="email-task-name">WH-001: Cập nhật quy trình tuyển dụng Q3 2025</p>
                                <p class="mt-1 text-[10px] text-gray-400" id="email-task-meta">Deadline: 25/07/2025 · Ưu tiên: Cao</p>
                            </div>

                            <a href="{{ route('dashboard.tasks') }}" class="w-full py-2.5 rounded-xl text-white font-semibold text-center block bg-[#003DA5] hover:bg-[#0057C8] transition-all text-xs">
                                Xem chi tiết công việc →
                            </a>
                        </div>
                        <!-- Footer -->
                        <div class="p-3 border-t border-gray-100 text-center space-y-0.5 text-gray-400">
                            <p>© 2025 MobiFone · 59 Lý Thường Kiệt, Hà Nội</p>
                            <a href="#" class="text-[#003DA5] hover:underline">Hủy đăng ký thông báo</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Email Notification settings -->
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                <h3 class="font-bold mb-4 text-[#001F5B]">Cài đặt thông báo email</h3>
                <div class="space-y-4">
                    @php
                        $settings = [
                            ['Được giao công việc', true],
                            ['Deadline sắp đến (1 ngày)', true],
                            ['Công việc quá hạn', true],
                            ['Có bình luận mới', false],
                            ['Công việc hoàn thành', false]
                        ];
                    @endphp
                    @foreach($settings as $set)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700 font-medium">{{ $set[0] }}</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer" {{ $set[1] ? 'checked' : '' }} onclick="toggleSetting(this)">
                                <div class="w-10 h-5 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:height-4 after:w-4 after:transition-all peer-checked:bg-[#003DA5]"></div>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Tab switching client side logic
    window.switchTab = (tab, btn) => {
        // Update active class on buttons
        const buttons = document.querySelectorAll('#notif-tabs button');
        buttons.forEach(b => {
            b.className = "px-3.5 py-1.5 rounded-full text-xs font-semibold text-gray-400 hover:text-gray-600 flex items-center gap-1.5";
        });
        btn.className = "px-3.5 py-1.5 rounded-full text-xs font-semibold bg-[#003DA5] text-white";

        // Filter list items
        const items = document.querySelectorAll('.notif-item');
        items.forEach(item => {
            if (tab === 'all') {
                item.style.display = 'flex';
            } else if (tab === 'unread') {
                item.style.display = item.getAttribute('data-read') === 'false' ? 'flex' : 'none';
            } else if (tab === 'task') {
                item.style.display = item.getAttribute('data-type') === 'task' ? 'flex' : 'none';
            } else if (tab === 'deadline') {
                item.style.display = item.getAttribute('data-type') === 'deadline' ? 'flex' : 'none';
            }
        });
    };

    // Mark single notif as read
    window.markAsRead = (el) => {
        el.classList.remove('bg-[#E8F0FE]/40');
        el.setAttribute('data-read', 'true');
        const dot = el.querySelector('.unread-dot');
        if (dot) {
            dot.className = "w-2 h-2 rounded-full mt-1.5 flex-shrink-0 bg-transparent unread-dot";
        }
    };

    // Mark all as read
    window.markAllAsRead = () => {
        const items = document.querySelectorAll('.notif-item');
        items.forEach(item => markAsRead(item));
    };

    // Switch preview email templates
    window.changeEmailPreview = (type) => {
        const subject = document.getElementById('email-subject');
        const bodyText = document.getElementById('email-body-text');
        const cardBox = document.getElementById('email-card-box');
        const taskName = document.getElementById('email-task-name');
        const taskMeta = document.getElementById('email-task-meta');

        if (type === 'task') {
            subject.innerText = "Tiêu đề: [WorkHub] Bạn được giao công việc mới";
            bodyText.innerHTML = "Bạn được giao công việc mới trong hệ thống WorkHub:";
            taskName.innerText = "WH-001: Cập nhật quy trình tuyển dụng Q3 2025";
            taskMeta.innerText = "Deadline: 25/07/2025 · Ưu tiên: Cao";
            cardBox.style.borderColor = "#CBD5E1";
        } else if (type === 'deadline') {
            subject.innerText = "Tiêu đề: [WorkHub] Nhắc nhở: Hạn chót công việc sắp đến";
            bodyText.innerHTML = "Công việc sau của bạn sắp đến hạn chót hoàn thành:";
            taskName.innerText = "WH-001: Cập nhật quy trình tuyển dụng Q3 2025";
            taskMeta.innerText = "Deadline: Còn 2 ngày nữa (Hạn: 25/07/2025)";
            cardBox.style.borderColor = "#D97706";
        } else if (type === 'overdue') {
            subject.innerText = "Tiêu đề: [WorkHub] Cảnh báo: Công việc đã quá hạn chót";
            bodyText.innerHTML = "Chú ý: Công việc dưới đây đã quá hạn nhưng chưa được hoàn thành:";
            taskName.innerText = "WH-002: Báo cáo KPI tháng 6 phòng Nhân sự";
            taskMeta.innerText = "Quá hạn: 3 ngày · Phụ trách: Trần Thị Bích";
            cardBox.style.borderColor = "#DC2626";
        }
    };

    // Cài đặt email toggle
    window.toggleSetting = (el) => {
        // Có thể thực hiện thông báo lưu thành công qua toast
    };
</script>
@endsection
