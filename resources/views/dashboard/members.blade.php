@extends('layouts.dashboard')

@section('title', 'Quản lý Thành viên - MobiFone WorkHub')
@section('page_title', 'Thành viên')

@section('content')
@php
    $membersById = collect($usersList->items())->keyBy('db_id');
    $visibleMembers = collect($usersList->items());
    $activeVisibleMembers = $visibleMembers->where('status', 'active')->count();
    $managerVisibleMembers = $visibleMembers->filter(fn ($member) => in_array($member['role'], ['Giám đốc', 'Trưởng phòng']))->count();
    $visibleTaskCount = $visibleMembers->sum('tasks');
@endphp

<style>
    .member-board { background: #fff; border: 1px solid #D8E4F5; border-radius: 8px; overflow: hidden; box-shadow: 0 18px 45px rgba(0, 31, 91, .06); }
    .member-board-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; padding: 20px; background: linear-gradient(135deg, #F6FAFF 0%, #FFFFFF 58%); border-bottom: 1px solid #E5EDF8; }
    .member-kicker { color: #E4002B; font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: .16em; }
    .member-heading { margin-top: 6px; color: #001F5B; font-size: 24px; line-height: 1.15; font-weight: 900; }
    .member-subtitle { margin-top: 6px; color: #5F6F89; font-size: 13px; max-width: 640px; }
    .member-count-pill { display: inline-flex; align-items: center; gap: 7px; min-height: 32px; padding: 0 12px; border-radius: 999px; background: #E8F0FE; color: #003DA5; font-size: 12px; font-weight: 900; }
    .member-add-btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; height: 42px; padding: 0 16px; border-radius: 8px; color: #fff; background: #003DA5; font-size: 13px; font-weight: 900; transition: background .16s ease, transform .16s ease, box-shadow .16s ease; white-space: nowrap; }
    .member-add-btn:hover { background: #0057C8; box-shadow: 0 12px 24px rgba(0, 61, 165, .18); transform: translateY(-1px); }
    .member-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap; padding: 16px 20px; background: #fff; border-bottom: 1px solid #EEF2F7; }
    .member-filter { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .member-filter-field { position: relative; }
    .member-filter-field i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: #8AA0BD; }
    .member-input, .member-select { height: 40px; border: 1px solid #D8E4F5; border-radius: 8px; background: #F8FAFF; color: #001F5B; font-size: 13px; outline: none; transition: border-color .16s ease, box-shadow .16s ease, background .16s ease; }
    .member-input { width: 280px; padding: 0 12px 0 38px; }
    .member-select { padding: 0 34px 0 12px; }
    .member-input:focus, .member-select:focus { background: #fff; border-color: #003DA5; box-shadow: 0 0 0 3px rgba(0, 61, 165, .10); }
    .member-filter-btn { height: 40px; padding: 0 14px; border-radius: 8px; background: #001F5B; color: #fff; font-size: 13px; font-weight: 800; }
    .member-clear { color: #E4002B; font-size: 12px; font-weight: 800; }
    .member-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; padding: 16px 20px; background: #F6FAFF; border-bottom: 1px solid #E5EDF8; }
    .member-stat { border: 1px solid #D8E4F5; border-radius: 8px; background: #fff; padding: 14px; }
    .member-stat-label { color: #64748B; font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: .08em; }
    .member-stat-value { margin-top: 8px; color: #001F5B; font-size: 24px; line-height: 1; font-weight: 900; }
    .member-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; padding: 18px 20px; background: linear-gradient(180deg, #FFFFFF 0%, #F8FAFF 100%); }
    .member-card { position: relative; overflow: hidden; border: 1px solid #D8E4F5; border-radius: 8px; background: #fff; min-width: 0; transition: transform .16s ease, border-color .16s ease, box-shadow .16s ease; }
    .member-card::before { content: ""; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: #E4002B; }
    .member-card:hover { transform: translateY(-2px); border-color: #B9CDF5; box-shadow: 0 14px 30px rgba(0, 31, 91, .08); }
    .member-card-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; padding: 16px 16px 12px 18px; }
    .member-identity { display: flex; align-items: center; gap: 12px; min-width: 0; }
    .member-avatar-lg { position: relative; width: 52px; height: 52px; border-radius: 8px; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 15px; font-weight: 900; flex-shrink: 0; box-shadow: inset 5px 0 0 rgba(228, 0, 43, .95); }
    .member-avatar-lg::after { content: ""; position: absolute; right: -1px; bottom: -1px; width: 12px; height: 12px; border-radius: 999px; background: #22C55E; border: 3px solid #fff; }
    .member-avatar-lg.inactive::after { background: #94A3B8; }
    .member-card-name { color: #001F5B; font-size: 15px; line-height: 1.25; font-weight: 900; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .member-card-email { margin-top: 4px; color: #64748B; font-size: 12px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .member-actions { display: flex; align-items: center; gap: 6px; flex-shrink: 0; }
    .member-icon-btn { width: 34px; height: 34px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid #D8E4F5; background: #fff; color: #003DA5; transition: background .16s ease, border-color .16s ease, color .16s ease; }
    .member-icon-btn:hover { background: #E8F0FE; border-color: #B9CDF5; }
    .member-icon-btn.danger { color: #E4002B; }
    .member-icon-btn.success { color: #15803D; }
    .member-meta-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 10px; padding: 0 16px 16px 18px; }
    .member-meta { min-width: 0; border-radius: 8px; background: #F8FAFF; border: 1px solid #EEF2F7; padding: 10px; }
    .member-meta-label { color: #7B8BA5; font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: .06em; }
    .member-meta-value { margin-top: 5px; color: #001F5B; font-size: 13px; font-weight: 800; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .member-card-bottom { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 12px 16px 14px 18px; border-top: 1px solid #EEF2F7; background: #FBFDFF; }
    .member-role-pill, .member-task-pill, .member-status-pill { display: inline-flex; align-items: center; gap: 6px; min-height: 28px; padding: 0 10px; border-radius: 999px; font-size: 12px; font-weight: 900; white-space: nowrap; }
    .member-role-pill.admin { background: #001F5B; color: #fff; }
    .member-role-pill.manager { background: #003DA5; color: #fff; }
    .member-role-pill.employee { background: #F1F5F9; color: #334155; border: 1px solid #E2E8F0; }
    .member-task-pill { background: #E8F0FE; color: #003DA5; }
    .member-status-pill.active { color: #15803D; background: #ECFDF5; }
    .member-status-pill.inactive { color: #64748B; background: #F1F5F9; }
    .member-empty { grid-column: 1 / -1; padding: 44px 20px; border: 1px dashed #B9CDF5; border-radius: 8px; background: #F6FAFF; text-align: center; color: #64748B; }
    .member-empty i { width: 34px; height: 34px; margin: 0 auto 10px; color: #003DA5; }
    .member-footer { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 14px 20px; border-top: 1px solid #E5EDF8; background: #fff; }
    @media (max-width: 1200px) { .member-grid { grid-template-columns: 1fr; } }
    @media (max-width: 820px) { .member-board-head, .member-toolbar, .member-footer { align-items: stretch; flex-direction: column; } .member-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); } .member-input { width: 100%; } .member-filter, .member-filter-field { width: 100%; } .member-select, .member-filter-btn { width: 100%; } }
    @media (max-width: 560px) { .member-card-top, .member-card-bottom { align-items: flex-start; flex-direction: column; } .member-actions { width: 100%; justify-content: flex-end; } .member-meta-grid { grid-template-columns: 1fr; } .member-stats { grid-template-columns: 1fr; } }
</style>

<div class="space-y-5 relative">
    <div class="member-board">
        <div class="member-board-head">
            <div>
                <div class="member-kicker">People operations</div>
                <h1 class="member-heading">Quản lý thành viên</h1>
                <p class="member-subtitle">Theo dõi nhân sự, phòng ban, vai trò và trạng thái tài khoản trong một giao diện gọn, dễ quét.</p>
            </div>
            <div class="flex items-center gap-3 flex-wrap justify-end">
                <span class="member-count-pill"><i data-lucide="users" class="w-4 h-4"></i>{{ $usersList->total() }} người</span>
                <button id="open-member-panel" class="member-add-btn">
                    <i data-lucide="plus" class="w-4 h-4"></i> Thêm thành viên
                </button>
            </div>
        </div>

        <div class="member-toolbar">
            <form method="GET" action="{{ route('dashboard.members') }}" class="member-filter">
                <div class="member-filter-field">
                    <i data-lucide="search"></i>
                    <input name="search" value="{{ $search }}" class="member-input" placeholder="Tìm tên, email hoặc mã NV..." />
                </div>

                <select name="dept" onchange="this.form.submit()" class="member-select">
                    <option value="">Tất cả phòng ban</option>
                    @foreach($departments as $d)
                        <option value="{{ $d->TENPHONG }}" {{ $deptFilter == $d->TENPHONG ? 'selected' : '' }}>{{ $d->TENPHONG }}</option>
                    @endforeach
                </select>

                <button type="submit" class="member-filter-btn">Lọc</button>
                @if(!empty($search) || !empty($deptFilter))
                    <a href="{{ route('dashboard.members') }}" class="member-clear">Xóa lọc</a>
                @endif
            </form>

            @if(session('success'))
                <div class="px-4 py-2 rounded-xl text-sm bg-green-50 border border-green-200 text-green-700 flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="px-4 py-2 rounded-xl text-sm bg-red-50 border border-red-200 text-red-700 flex items-center gap-2">
                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
        </div>

        <div class="member-stats">
            <div class="member-stat">
                <div class="member-stat-label">Đang hiển thị</div>
                <div class="member-stat-value">{{ $visibleMembers->count() }}</div>
            </div>
            <div class="member-stat">
                <div class="member-stat-label">Hoạt động</div>
                <div class="member-stat-value">{{ $activeVisibleMembers }}</div>
            </div>
            <div class="member-stat">
                <div class="member-stat-label">Quản trị</div>
                <div class="member-stat-value">{{ $managerVisibleMembers }}</div>
            </div>
            <div class="member-stat">
                <div class="member-stat-label">Việc đang giao</div>
                <div class="member-stat-value">{{ $visibleTaskCount }}</div>
            </div>
        </div>

        <div class="member-grid">
            @forelse($usersList as $m)
                @php
                    $roleClass = $m['role'] == 'Giám đốc' ? 'admin' : ($m['role'] == 'Trưởng phòng' ? 'manager' : 'employee');
                    $statusLabel = $m['status'] == 'active' ? 'Hoạt động' : 'Vô hiệu';
                @endphp
                <article class="member-card">
                    <div class="member-card-top">
                        <div class="member-identity">
                            <div class="member-avatar-lg {{ $m['status'] == 'active' ? '' : 'inactive' }}" style="background-color: {{ $m['color'] }};">
                                {{ $m['avatar'] }}
                            </div>
                            <div class="min-w-0">
                                <div class="member-card-name">{{ $m['name'] }}</div>
                                <div class="member-card-email">{{ $m['email'] }}</div>
                            </div>
                        </div>
                        <div class="member-actions">
                            <button type="button"
                                    data-member-id="{{ $m['db_id'] }}"
                                    class="edit-member-btn member-icon-btn"
                                    title="Chỉnh sửa"
                                    aria-label="Chỉnh sửa {{ $m['name'] }}">
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </button>
                            <button type="button"
                                    data-member-id="{{ $m['db_id'] }}"
                                    class="role-member-btn member-icon-btn"
                                    title="Phân quyền"
                                    aria-label="Phân quyền {{ $m['name'] }}">
                                <i data-lucide="shield" class="w-4 h-4"></i>
                            </button>
                            <form method="POST"
                                  action="{{ route('dashboard.members.status', $m['db_id']) }}"
                                  onsubmit="return confirm('{{ $m['status'] == 'active' ? 'Vô hiệu hóa tài khoản '.$m['name'].'?' : 'Kích hoạt lại tài khoản '.$m['name'].'?' }}')">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="member-icon-btn {{ $m['status'] == 'active' ? 'danger' : 'success' }}"
                                        title="{{ $m['status'] == 'active' ? 'Vô hiệu hóa' : 'Kích hoạt lại' }}"
                                        aria-label="{{ $m['status'] == 'active' ? 'Vô hiệu hóa '.$m['name'] : 'Kích hoạt lại '.$m['name'] }}">
                                    <i data-lucide="{{ $m['status'] == 'active' ? 'alert-circle' : 'rotate-ccw' }}" class="w-4 h-4"></i>
                                </button>
                            </form>
                            <form method="POST"
                                  action="{{ route('dashboard.members.delete', $m['db_id']) }}"
                                  onsubmit="return confirm('Bạn có chắc chắn muốn XÓA vĩnh viễn nhân viên {{ addslashes($m['name']) }} khỏi hệ thống không?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="member-icon-btn danger"
                                        title="Xóa nhân viên"
                                        aria-label="Xóa {{ $m['name'] }}">
                                    <i data-lucide="trash-2" class="w-4 h-4 text-red-600"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="member-meta-grid">
                        <div class="member-meta">
                            <div class="member-meta-label">Mã NV</div>
                            <div class="member-meta-value">{{ $m['id'] }}</div>
                        </div>
                        <div class="member-meta">
                            <div class="member-meta-label">Phòng ban</div>
                            <div class="member-meta-value">{{ $m['dept'] }}</div>
                        </div>
                        <div class="member-meta">
                            <div class="member-meta-label">Ngày vào</div>
                            <div class="member-meta-value">{{ $m['joined'] }}</div>
                        </div>
                    </div>

                    <div class="member-card-bottom">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="member-role-pill {{ $roleClass }}">{{ $m['role'] }}</span>
                            <span class="member-task-pill"><i data-lucide="briefcase" class="w-3.5 h-3.5"></i>{{ $m['tasks'] }} việc</span>
                        </div>
                        <span class="member-status-pill {{ $m['status'] == 'active' ? 'active' : 'inactive' }}">
                            <i data-lucide="{{ $m['status'] == 'active' ? 'check-circle-2' : 'pause-circle' }}" class="w-3.5 h-3.5"></i>{{ $statusLabel }}
                        </span>
                    </div>
                </article>
            @empty
                <div class="member-empty" role="status">
                    <i data-lucide="user-round-search"></i>
                    <div style="font-weight:900;color:#001F5B">Không tìm thấy thành viên phù hợp</div>
                    <div style="font-size:13px;margin-top:4px">Thử đổi từ khóa tìm kiếm hoặc chọn phòng ban khác.</div>
                </div>
            @endforelse
        </div>

        <div class="member-footer">
            <div class="flex items-center gap-2 text-sm text-gray-400">
                Hiển thị {{ $usersList->firstItem() ?? 0 }}-{{ $usersList->lastItem() ?? 0 }} / {{ $usersList->total() }} mục
            </div>
            <div class="member-pagination">
                {{ $usersList->onEachSide(1)->links() }}
            </div>
        </div>
    </div>
</div>

<div class="hidden">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <h1 class="text-2xl font-bold text-[#001F5B]">Quản lý Thành viên</h1>
            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#E8F0FE] text-[#003DA5]">{{ $usersList->total() }} người</span>
        </div>
        <button id="legacy-open-member-panel"
                class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white bg-[#003DA5] hover:bg-[#0057C8] hover:shadow-lg transition-all active:scale-95">
            <i data-lucide="plus" class="w-4 h-4"></i> Thêm thành viên
        </button>
    </div>

    <!-- Filters & Messages -->
    <div class="flex items-center gap-3 flex-wrap justify-between">
        <form method="GET" action="{{ route('dashboard.members') }}" class="flex gap-3 flex-wrap items-center">
            <!-- Search -->
            <div class="relative">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                <input name="search" value="{{ $search }}"
                       class="pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-xl outline-none w-64 bg-white focus:border-[#003DA5]"
                       placeholder="Tìm tên, email hoặc mã NV..." />
            </div>

            <!-- Dept Selector -->
            <select name="dept" onchange="this.form.submit()" class="px-3 py-2 text-sm border border-gray-200 rounded-xl outline-none bg-white text-gray-700 focus:border-[#003DA5]">
                <option value="">Tất cả phòng ban</option>
                @foreach($departments as $d)
                    <option value="{{ $d->TENPHONG }}" {{ $deptFilter == $d->TENPHONG ? 'selected' : '' }}>{{ $d->TENPHONG }}</option>
                @endforeach
            </select>

            <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition-all">Lọc</button>
            @if(!empty($search) || !empty($deptFilter))
                <a href="{{ route('dashboard.members') }}" class="text-xs text-red-600 hover:underline">Xóa lọc</a>
            @endif
        </form>

        @if(session('success'))
            <div class="px-4 py-2 rounded-xl text-sm bg-green-50 border border-green-200 text-green-700 flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="px-4 py-2 rounded-xl text-sm bg-red-50 border border-red-200 text-red-700 flex items-center gap-2">
                <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif
    </div>

    <!-- Members Table -->
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-[#F1F3F5] text-gray-400 font-semibold text-xs uppercase tracking-wide">
                        <th class="px-4 py-3">Nhân viên</th>
                        <th class="px-4 py-3">Mã NV</th>
                        <th class="px-4 py-3">Phòng ban</th>
                        <th class="px-4 py-3">Chức vụ</th>
                        <th class="px-4 py-3">Vai trò</th>
                        <th class="px-4 py-3">CV đang giao</th>
                        <th class="px-4 py-3">Ngày tham gia</th>
                        <th class="px-4 py-3">Trạng thái</th>
                        <th class="px-4 py-3">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($usersList as $m)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="relative flex-shrink-0">
                                        <div class="w-10 h-10 rounded-full text-white flex items-center justify-center font-bold text-sm" style="background-color: {{ $m['color'] }};">
                                            {{ $m['avatar'] }}
                                        </div>
                                        <div class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full bg-green-500 border-2 border-white"></div>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-700">{{ $m['name'] }}</div>
                                        <div class="text-xs text-gray-400">{{ $m['email'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-xs font-mono text-gray-400">{{ $m['id'] }}</td>
                            <td class="px-4 py-4 text-sm text-gray-700">{{ $m['dept'] }}</td>
                            <td class="px-4 py-4 text-sm text-gray-700">{{ $m['position'] }}</td>
                            <td class="px-4 py-4">
                                @if($m['role'] == 'Giám đốc')
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#001F5B] text-white border border-[#001F5B]">{{ $m['role'] }}</span>
                                @elseif($m['role'] == 'Trưởng phòng')
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#003DA5] text-white border border-[#003DA5]">{{ $m['role'] }}</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">{{ $m['role'] }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#E8F0FE] text-[#003DA5]">{{ $m['tasks'] }}</span>
                            </td>
                            <td class="px-4 py-4 text-xs text-gray-400">{{ $m['joined'] }}</td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-1.5">
                                    @if($m['status'] == 'active')
                                        <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                        <span class="text-xs font-semibold text-green-600">Hoạt động</span>
                                    @else
                                        <div class="w-2 h-2 rounded-full bg-gray-400"></div>
                                        <span class="text-xs font-semibold text-gray-500">Vô hiệu</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-1">
                                    <button type="button"
                                            data-member-id="{{ $m['db_id'] }}"
                                            class="edit-member-btn w-7 h-7 rounded-lg flex items-center justify-center hover:bg-blue-50 text-[#003DA5]"
                                            title="Chỉnh sửa"
                                            aria-label="Chỉnh sửa {{ $m['name'] }}">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </button>
                                    <button type="button"
                                            data-member-id="{{ $m['db_id'] }}"
                                            class="role-member-btn w-7 h-7 rounded-lg flex items-center justify-center hover:bg-blue-50 text-[#003DA5]"
                                            title="Phân quyền"
                                            aria-label="Phân quyền {{ $m['name'] }}">
                                        <i data-lucide="shield" class="w-4 h-4"></i>
                                    </button>
                                    <form method="POST"
                                          action="{{ route('dashboard.members.status', $m['db_id']) }}"
                                          onsubmit="return confirm('{{ $m['status'] == 'active' ? 'Vô hiệu hóa tài khoản '.$m['name'].'?' : 'Kích hoạt lại tài khoản '.$m['name'].'?' }}')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="w-7 h-7 rounded-lg flex items-center justify-center {{ $m['status'] == 'active' ? 'hover:bg-red-50 text-red-600' : 'hover:bg-green-50 text-green-600' }}"
                                                title="{{ $m['status'] == 'active' ? 'Vô hiệu hóa' : 'Kích hoạt lại' }}"
                                                aria-label="{{ $m['status'] == 'active' ? 'Vô hiệu hóa '.$m['name'] : 'Kích hoạt lại '.$m['name'] }}">
                                            <i data-lucide="{{ $m['status'] == 'active' ? 'alert-circle' : 'rotate-ccw' }}" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                    <form method="POST"
                                          action="{{ route('dashboard.members.delete', $m['db_id']) }}"
                                          onsubmit="return confirm('Bạn có chắc chắn muốn XÓA vĩnh viễn nhân viên {{ addslashes($m['name']) }} khỏi hệ thống không?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="w-7 h-7 rounded-lg flex items-center justify-center hover:bg-red-50 text-red-600"
                                                title="Xóa nhân viên"
                                                aria-label="Xóa {{ $m['name'] }}">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-8 text-gray-400 text-sm">Không tìm thấy thành viên phù hợp.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-2 text-sm text-gray-400">
                Hiển thị {{ $usersList->firstItem() ?? 0 }}-{{ $usersList->lastItem() ?? 0 }} / {{ $usersList->total() }} mục
            </div>
            <div class="member-pagination">
                {{ $usersList->onEachSide(1)->links() }}
            </div>
        </div>
    </div>
</div>

<!-- ==================== POPUP: SLIDE MEMBER PANEL ==================== -->
<div id="member-panel" class="fixed inset-0 z-50 flex justify-end bg-black/30 hidden">
    <div class="w-96 bg-white h-full shadow-2xl flex flex-col justify-between" id="panel-content">
        <div class="px-6 py-5 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-bold text-[#001F5B]">Thêm thành viên mới</h2>
            <button id="close-member-panel"><i data-lucide="x" class="w-5 h-5 text-gray-400"></i></button>
        </div>

        <form action="{{ route('dashboard.members.save') }}" method="POST" class="p-6 space-y-4 overflow-y-auto flex-1 custom-scrollbar">
            @csrf
            <!-- Name -->
            <div>
                <label class="block text-sm font-semibold mb-1.5 text-gray-700 font-medium">Họ và tên *</label>
                <input name="name" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:border-[#003DA5]" placeholder="Nhập họ và tên..." />
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-semibold mb-1.5 text-gray-700 font-medium font-medium">Email công ty *</label>
                <input type="email" name="email" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:border-[#003DA5]" placeholder="vi_du@mobifone.vn" />
            </div>

            <!-- Password -->
            <div>
                <label class="block text-sm font-semibold mb-1.5 text-gray-700 font-medium">Mật khẩu khởi tạo *</label>
                <div class="relative">
                    <input type="password" name="password" required class="password-eye-input w-full px-4 py-2.5 pr-11 border border-gray-200 rounded-xl text-sm outline-none focus:border-[#003DA5]" placeholder="••••••••" />
                    <button type="button" class="password-eye-toggle absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#003DA5] focus:outline-none focus:ring-2 focus:ring-[#003DA5]/25 rounded-lg" aria-label="Hiện mật khẩu" title="Hiện mật khẩu">
                        <i data-lucide="eye" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>

            <!-- Dept -->
            <div>
                <label class="block text-sm font-semibold mb-1.5 text-gray-700 font-medium">Phòng ban *</label>
                <select name="department_id" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none bg-white focus:border-[#003DA5]">
                    <option value="">— Chọn phòng ban —</option>
                    @foreach($departments as $d)
                        <option value="{{ $d->id }}">{{ $d->TENPHONG }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Role Selector -->
            <div>
                <label class="block text-sm font-semibold mb-2 text-gray-700 font-medium">Vai trò hệ thống *</label>
                @foreach($roles as $r)
                    @php
                        $desc = $r->name == 'Giám đốc' ? 'Toàn quyền điều hành hệ thống' : ($r->name == 'Trưởng phòng' ? 'Tạo và giao task, quản lý phòng ban' : 'Nhận và cập nhật task được giao');
                    @endphp
                    <label class="flex items-start gap-3 p-3 rounded-xl border border-gray-200 mb-2 cursor-pointer hover:bg-gray-50/50">
                        <input type="radio" name="role_id" value="{{ $r->id }}" class="mt-1 text-[#003DA5] focus:ring-[#003DA5]" required {{ $r->name == 'Nhân viên' ? 'checked' : '' }} />
                        <div>
                            <div class="text-sm font-semibold text-gray-700">{{ $r->name }}</div>
                            <div class="text-xs text-gray-400 mt-0.5">{{ $desc }}</div>
                        </div>
                    </label>
                @endforeach
            </div>

            <!-- Onboarding email option -->
            <div class="flex items-center justify-between p-4 rounded-xl bg-[#E8F0FE]">
                <div>
                    <p class="text-sm font-semibold text-[#001F5B]">Gửi email mời tham gia</p>
                    <p class="text-xs text-gray-400 mt-0.5">Email onboarding tự động</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="send_email" value="1" class="sr-only peer" checked>
                    <div class="w-10 h-5 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:height-4 after:w-4 after:transition-all peer-checked:bg-[#003DA5]"></div>
                </label>
            </div>

            <!-- Submit -->
            <div class="pt-4">
                <button type="submit" class="w-full py-3 rounded-xl text-sm font-bold text-white bg-[#003DA5] hover:bg-[#0057C8] transition-all">Gửi lời mời thành viên</button>
            </div>
        </form>
    </div>
</div>

<div id="edit-member-panel" class="fixed inset-0 z-50 flex justify-end bg-black/30 hidden">
    <div class="w-96 bg-white h-full shadow-2xl flex flex-col justify-between">
        <div class="px-6 py-5 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-[#001F5B]">Chỉnh sửa thành viên</h2>
                <p class="text-xs text-gray-400 mt-0.5" id="edit-member-subtitle"></p>
            </div>
            <button type="button" id="close-edit-member-panel" aria-label="Đóng"><i data-lucide="x" class="w-5 h-5 text-gray-400"></i></button>
        </div>

        <form id="edit-member-form" method="POST" class="p-6 space-y-4 overflow-y-auto flex-1 custom-scrollbar">
            @csrf
            @method('PATCH')
            <div>
                <label class="block text-sm font-semibold mb-1.5 text-gray-700">Họ và tên *</label>
                <input id="edit_member_name" name="name" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:border-[#003DA5]" />
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1.5 text-gray-700">Email công ty *</label>
                <input id="edit_member_email" type="email" name="email" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:border-[#003DA5]" />
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1.5 text-gray-700">Mật khẩu mới</label>
                <div class="relative">
                    <input type="password" name="password" class="password-eye-input w-full px-4 py-2.5 pr-11 border border-gray-200 rounded-xl text-sm outline-none focus:border-[#003DA5]" placeholder="Bỏ trống nếu không đổi" />
                    <button type="button" class="password-eye-toggle absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#003DA5] focus:outline-none focus:ring-2 focus:ring-[#003DA5]/25 rounded-lg" aria-label="Hiện mật khẩu" title="Hiện mật khẩu">
                        <i data-lucide="eye" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1.5 text-gray-700">Phòng ban *</label>
                <select id="edit_member_department" name="department_id" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none bg-white focus:border-[#003DA5]">
                    @foreach($departments as $d)
                        <option value="{{ $d->id }}">{{ $d->TENPHONG }}</option>
                    @endforeach
                </select>
            </div>
            <div class="pt-4">
                <button type="submit" class="w-full py-3 rounded-xl text-sm font-bold text-white bg-[#003DA5] hover:bg-[#0057C8] transition-all">Lưu thay đổi</button>
            </div>
        </form>
    </div>
</div>

<div id="role-member-panel" class="fixed inset-0 z-50 flex justify-end bg-black/30 hidden">
    <div class="w-96 bg-white h-full shadow-2xl flex flex-col justify-between">
        <div class="px-6 py-5 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-[#001F5B]">Phân quyền thành viên</h2>
                <p class="text-xs text-gray-400 mt-0.5" id="role-member-subtitle"></p>
            </div>
            <button type="button" id="close-role-member-panel" aria-label="Đóng"><i data-lucide="x" class="w-5 h-5 text-gray-400"></i></button>
        </div>

        <form id="role-member-form" method="POST" class="p-6 space-y-4 overflow-y-auto flex-1 custom-scrollbar">
            @csrf
            @method('PATCH')
            <div>
                <label class="block text-sm font-semibold mb-2 text-gray-700">Vai trò hệ thống</label>
                @foreach($roles as $r)
                    @php
                        $desc = $r->name == 'Giám đốc' ? 'Toàn quyền điều hành hệ thống' : ($r->name == 'Trưởng phòng' ? 'Tạo và giao việc, quản lý phòng ban' : 'Nhận và cập nhật việc được giao');
                    @endphp
                    <label class="flex items-start gap-3 p-3 rounded-xl border border-gray-200 mb-2 cursor-pointer hover:bg-gray-50/50">
                        <input type="radio" name="role_id" value="{{ $r->id }}" class="role-radio mt-1 text-[#003DA5] focus:ring-[#003DA5]" required />
                        <div>
                            <div class="text-sm font-semibold text-gray-700">{{ $r->name }}</div>
                            <div class="text-xs text-gray-400 mt-0.5">{{ $desc }}</div>
                        </div>
                    </label>
                @endforeach
            </div>
            <div class="rounded-xl bg-[#E8F0FE] p-4 text-xs text-[#001F5B] leading-relaxed">
                Thay đổi vai trò sẽ ảnh hưởng đến menu và phạm vi thao tác của thành viên ở lần truy cập tiếp theo.
            </div>
            <div class="pt-4">
                <button type="submit" class="w-full py-3 rounded-xl text-sm font-bold text-white bg-[#003DA5] hover:bg-[#0057C8] transition-all">Cập nhật vai trò</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const membersById = @json($membersById);
        const membersBaseUrl = @json(url('/dashboard/members'));
        const panel = document.getElementById('member-panel');
        const openBtn = document.getElementById('open-member-panel');
        const closeBtn = document.getElementById('close-member-panel');
        const editPanel = document.getElementById('edit-member-panel');
        const rolePanel = document.getElementById('role-member-panel');
        const editForm = document.getElementById('edit-member-form');
        const roleForm = document.getElementById('role-member-form');

        const togglePanel = () => panel.classList.toggle('hidden');
        const closeEditPanel = () => editPanel.classList.add('hidden');
        const closeRolePanel = () => rolePanel.classList.add('hidden');

        openBtn.addEventListener('click', togglePanel);
        closeBtn.addEventListener('click', togglePanel);
        document.getElementById('close-edit-member-panel').addEventListener('click', closeEditPanel);
        document.getElementById('close-role-member-panel').addEventListener('click', closeRolePanel);

        document.querySelectorAll('.password-eye-toggle').forEach((button) => {
            button.addEventListener('click', () => {
                const input = button.parentElement.querySelector('.password-eye-input');
                const icon = button.querySelector('i');
                const shouldShow = input.type === 'password';

                input.type = shouldShow ? 'text' : 'password';
                icon.setAttribute('data-lucide', shouldShow ? 'eye-off' : 'eye');
                button.setAttribute('aria-label', shouldShow ? 'Ẩn mật khẩu' : 'Hiện mật khẩu');
                button.setAttribute('title', shouldShow ? 'Ẩn mật khẩu' : 'Hiện mật khẩu');
                lucide.createIcons();
            });
        });

        // Click outside panel to close it
        panel.addEventListener('click', (e) => {
            if (e.target === panel) togglePanel();
        });
        editPanel.addEventListener('click', (e) => {
            if (e.target === editPanel) closeEditPanel();
        });
        rolePanel.addEventListener('click', (e) => {
            if (e.target === rolePanel) closeRolePanel();
        });

        document.querySelectorAll('.edit-member-btn').forEach((button) => {
            button.addEventListener('click', () => {
                const member = membersById[button.dataset.memberId];
                if (!member) return;

                editForm.action = `${membersBaseUrl}/${member.db_id}`;
                document.getElementById('edit-member-subtitle').innerText = member.id + ' - ' + member.email;
                document.getElementById('edit_member_name').value = member.name;
                document.getElementById('edit_member_email').value = member.email;
                document.getElementById('edit_member_department').value = member.department_id;
                editPanel.classList.remove('hidden');
            });
        });

        document.querySelectorAll('.role-member-btn').forEach((button) => {
            button.addEventListener('click', () => {
                const member = membersById[button.dataset.memberId];
                if (!member) return;

                roleForm.action = `${membersBaseUrl}/${member.db_id}/role`;
                document.getElementById('role-member-subtitle').innerText = member.name + ' - ' + member.email;
                document.querySelectorAll('.role-radio').forEach((input) => {
                    input.checked = Number(input.value) === Number(member.role_id);
                });
                rolePanel.classList.remove('hidden');
            });
        });
    });
</script>
@endsection
