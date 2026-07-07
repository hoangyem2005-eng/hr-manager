@extends('layouts.dashboard')

@section('title', 'Thông báo & Cấu hình - MobiFone WorkHub')
@section('page_title', 'Thông báo')

@section('content')
<div class="space-y-5">
    <div class="flex items-start justify-between gap-4 flex-wrap">
        <div>
            <h1 class="text-2xl font-bold text-[#001F5B]">Thông báo nội bộ</h1>
            <p class="mt-1 text-sm text-slate-500">Soạn thông báo chung và theo dõi các thông báo đã nhận trong WorkHub.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">
        <div class="lg:col-span-3 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col justify-between">
            <div>
                <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between flex-wrap gap-2">
                    <div class="flex gap-1.5" id="notif-tabs">
                        <button type="button" onclick="switchTab('all', this)" class="px-3.5 py-1.5 rounded-full text-xs font-semibold bg-[#003DA5] text-white">Tất cả</button>
                        <button type="button" onclick="switchTab('unread', this)" class="px-3.5 py-1.5 rounded-full text-xs font-semibold text-gray-400 hover:text-gray-600 flex items-center gap-1.5">
                            Chưa đọc <span class="px-1.5 py-0.5 rounded-full bg-[#E4002B] text-white font-bold text-[9px]">{{ $unreadCount }}</span>
                        </button>
                        <button type="button" onclick="switchTab('task', this)" class="px-3.5 py-1.5 rounded-full text-xs font-semibold text-gray-400 hover:text-gray-600 flex items-center gap-1.5">
                            Task giao <span class="px-1.5 py-0.5 rounded-full bg-slate-100 text-slate-500 font-bold text-[9px]">{{ $notificationTypeCounts['task'] ?? 0 }}</span>
                        </button>
                        <button type="button" onclick="switchTab('deadline', this)" class="px-3.5 py-1.5 rounded-full text-xs font-semibold text-gray-400 hover:text-gray-600 flex items-center gap-1.5">
                            Nhắc deadline <span class="px-1.5 py-0.5 rounded-full bg-amber-100 text-amber-700 font-bold text-[9px]">{{ $notificationTypeCounts['deadline'] ?? 0 }}</span>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('dashboard.notifications.markAllRead') }}">
                        @csrf
                        <button type="submit" class="text-xs font-semibold text-[#003DA5] hover:underline">Đánh dấu tất cả đã đọc</button>
                    </form>
                </div>

                <div class="divide-y divide-gray-50" id="notif-list-container">
                    @forelse($notifications as $n)
                        @php
                            $colors = [
                                'general' => ['bg' => '#E8F0FE', 'text' => '#003DA5', 'icon' => 'megaphone'],
                                'task' => ['bg' => '#EFF6FF', 'text' => '#003DA5', 'icon' => 'check-square'],
                                'overdue' => ['bg' => '#FEF2F2', 'text' => '#DC2626', 'icon' => 'alert-circle'],
                                'deadline' => ['bg' => '#FFFBEB', 'text' => '#D97706', 'icon' => 'clock'],
                                'complete' => ['bg' => '#F0FDF4', 'text' => '#16A34A', 'icon' => 'check-circle'],
                            ];
                            $c = $colors[$n['type']] ?? $colors['task'];
                        @endphp
                        <form method="POST" action="{{ route('dashboard.notifications.open', $n['id']) }}" class="notif-item-form">
                            @csrf
                            <button type="submit"
                                class="w-full text-left flex gap-4 p-4 hover:bg-gray-50/70 transition-colors cursor-pointer notif-item border-0 {{ !$n['read'] ? 'bg-[#E8F0FE]/40' : 'bg-white' }}"
                                data-type="{{ $n['type'] }}"
                                data-read="{{ $n['read'] ? 'true' : 'false' }}">
                                <span class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0" style="background-color: {{ $c['bg'] }}; color: {{ $c['text'] }};">
                                    <i data-lucide="{{ $c['icon'] }}" class="w-5.5 h-5.5"></i>
                                </span>

                                <span class="flex-1 min-w-0">
                                    <span class="block text-sm font-bold text-[#001F5B]">{{ $n['title'] }}</span>
                                    <span class="block text-xs text-gray-500 mt-0.5 truncate">{{ $n['desc'] }}</span>
                                    <span class="block text-[10px] text-gray-400 mt-1.5 font-mono">{{ $n['time'] }}</span>
                                </span>

                                <span class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0 {{ !$n['read'] ? 'bg-[#003DA5]' : 'bg-transparent' }} unread-dot"></span>
                            </button>
                        </form>
                    @empty
                        <div class="p-8 text-center text-sm text-gray-500">
                            Chưa có thông báo nào.
                        </div>
                    @endforelse

                    <div id="notif-empty-filter" class="hidden p-10 text-center" role="status">
                        <span class="mx-auto w-12 h-12 rounded-full bg-[#E8F0FE] text-[#003DA5] flex items-center justify-center">
                            <i data-lucide="inbox" class="w-6 h-6"></i>
                        </span>
                        <h3 class="mt-3 text-sm font-bold text-[#001F5B]" id="notif-empty-title">Chưa có thông báo phù hợp</h3>
                        <p class="mt-1 text-xs text-slate-500" id="notif-empty-desc">Khi có dữ liệu mới, thông báo sẽ xuất hiện tại đây.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                @if($canBroadcastNotifications)
                    <div class="flex items-start justify-between gap-3 mb-5">
                        <div>
                            <h3 class="font-bold text-[#001F5B]">Gửi thông báo toàn hệ thống</h3>
                            <p class="mt-1 text-xs text-slate-500">Thông báo sẽ hiển thị cho {{ $broadcastRecipientCount }} nhân viên đang hoạt động.</p>
                        </div>
                        <span class="w-10 h-10 rounded-2xl bg-[#E8F0FE] text-[#003DA5] flex items-center justify-center">
                            <i data-lucide="send" class="w-5 h-5"></i>
                        </span>
                    </div>

                    <form method="POST" action="{{ route('dashboard.notifications.broadcast') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label for="broadcast-title" class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-1.5">Tiêu đề</label>
                            <input
                                id="broadcast-title"
                                name="title"
                                value="{{ old('title') }}"
                                maxlength="160"
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-[#001F5B] outline-none transition focus:border-[#003DA5] focus:ring-4 focus:ring-[#003DA5]/10"
                                placeholder="Ví dụ: Lịch bảo trì hệ thống"
                                required
                            >
                            @error('title')
                                <p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="broadcast-message" class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-1.5">Nội dung</label>
                            <textarea
                                id="broadcast-message"
                                name="message"
                                rows="7"
                                maxlength="1500"
                                class="w-full resize-none rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[#003DA5] focus:ring-4 focus:ring-[#003DA5]/10"
                                placeholder="Nhập nội dung cần thông báo cho toàn bộ nhân viên..."
                                required
                            >{{ old('message') }}</textarea>
                            @error('message')
                                <p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-[#003DA5] px-4 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-[#0057C8]">
                            <i data-lucide="send" class="w-4 h-4"></i>
                            Gửi đến toàn bộ nhân viên
                        </button>
                    </form>
                @else
                    <div class="flex gap-3">
                        <span class="w-10 h-10 rounded-2xl bg-[#E8F0FE] text-[#003DA5] flex items-center justify-center flex-shrink-0">
                            <i data-lucide="bell" class="w-5 h-5"></i>
                        </span>
                        <div>
                            <h3 class="font-bold text-[#001F5B]">Thông báo của bạn</h3>
                            <p class="mt-1 text-sm text-slate-500">Các thông báo từ quản lý sẽ xuất hiện ở danh sách bên trái. Bạn có thể mở từng thông báo hoặc đánh dấu tất cả đã đọc.</p>
                        </div>
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                <h3 class="font-bold text-[#001F5B]">Trạng thái hộp thông báo</h3>
                <div class="mt-4 grid grid-cols-2 gap-3">
                    <div class="rounded-2xl bg-[#E8F0FE] p-4">
                        <p class="text-xs font-bold uppercase tracking-wide text-[#003DA5]">Chưa đọc</p>
                        <p class="mt-2 text-2xl font-black text-[#001F5B]">{{ $unreadCount }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Tổng số</p>
                        <p class="mt-2 text-2xl font-black text-[#001F5B]">{{ $notifications->count() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    window.switchTab = (tab, btn) => {
        const buttons = document.querySelectorAll('#notif-tabs button');
        buttons.forEach((button) => {
            button.className = 'px-3.5 py-1.5 rounded-full text-xs font-semibold text-gray-400 hover:text-gray-600 flex items-center gap-1.5';
        });
        btn.className = 'px-3.5 py-1.5 rounded-full text-xs font-semibold bg-[#003DA5] text-white flex items-center gap-1.5';

        const emptyState = document.getElementById('notif-empty-filter');
        const emptyTitle = document.getElementById('notif-empty-title');
        const emptyDesc = document.getElementById('notif-empty-desc');
        const emptyCopy = {
            all: ['Chưa có thông báo nào', 'Các thông báo mới sẽ xuất hiện tại đây.'],
            unread: ['Không còn thông báo chưa đọc', 'Bạn đã xử lý xong các thông báo mới.'],
            task: ['Chưa có thông báo giao việc', 'Khi quản lý giao công việc mới, thông báo sẽ tự xuất hiện trong tab này.'],
            deadline: ['Chưa có nhắc deadline', 'Các công việc sắp hết hạn trong 2 ngày tới sẽ được tự động nhắc tại đây.'],
        };
        let visibleCount = 0;

        document.querySelectorAll('.notif-item-form').forEach((form) => {
            const item = form.querySelector('.notif-item');
            if (tab === 'all') {
                form.style.display = 'block';
                visibleCount += 1;
            } else if (tab === 'unread') {
                const show = item.getAttribute('data-read') === 'false';
                form.style.display = show ? 'block' : 'none';
                visibleCount += show ? 1 : 0;
            } else {
                const show = item.getAttribute('data-type') === tab;
                form.style.display = show ? 'block' : 'none';
                visibleCount += show ? 1 : 0;
            }
        });

        if (emptyState) {
            const copy = emptyCopy[tab] || emptyCopy.all;
            emptyTitle.innerText = copy[0];
            emptyDesc.innerText = copy[1];
            emptyState.classList.toggle('hidden', visibleCount !== 0);
        }
    };

</script>
@endsection
